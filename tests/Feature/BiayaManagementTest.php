<?php

namespace Tests\Feature;

use App\Models\Biaya;
use App\Models\Kebun;
use App\Models\Panen;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BiayaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_update_and_delete_their_own_biaya(): void
    {
        $user = User::factory()->create();
        $kebun = $this->createKebun($user, 'Kebun Saya');
        $this->actingAs($user);

        $this->post(route('biaya.store'), $this->validBiayaData($kebun))
            ->assertRedirect(route('biaya.index'))
            ->assertSessionHas('success', 'Catatan biaya berhasil ditambahkan.');

        $biaya = Biaya::query()->where('keterangan', 'Pupuk awal')->firstOrFail();
        $this->assertSame($user->id, $biaya->user_id);
        $this->assertSame($kebun->id, $biaya->kebun_id);
        $this->assertSame(Biaya::MODE_MANUAL, $biaya->mode_hitung);

        $this->put(route('biaya.update', $biaya), [
            ...$this->validBiayaData($kebun),
            'kategori' => 'Perawatan',
            'nominal' => 650000,
            'keterangan' => 'Perawatan jalan kebun',
        ])->assertRedirect(route('biaya.index'))
            ->assertSessionHas('success', 'Catatan biaya berhasil diperbarui.');

        $this->assertDatabaseHas('biayas', [
            'id' => $biaya->id,
            'kategori' => 'Perawatan',
            'nominal' => 650000,
            'keterangan' => 'Perawatan jalan kebun',
            'mode_hitung' => 'manual',
            'tarif_per_kg' => null,
        ]);

        $this->get(route('biaya.index'))
            ->assertOk()
            ->assertSee('confirm(')
            ->assertSee('Hapus catatan biaya ini?');

        $this->delete(route('biaya.destroy', $biaya))
            ->assertRedirect(route('biaya.index'))
            ->assertSessionHas('success', 'Catatan biaya berhasil dihapus.');

        $this->assertDatabaseMissing('biayas', ['id' => $biaya->id]);
    }

    public function test_cost_validation_rejects_negative_amount_and_foreign_estate(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownKebun = $this->createKebun($user);
        $foreignKebun = $this->createKebun($otherUser, 'Kebun Orang Lain');

        $this->actingAs($user)
            ->from(route('biaya.create'))
            ->post(route('biaya.store'), [
                ...$this->validBiayaData($ownKebun),
                'nominal' => -100,
            ])
            ->assertRedirect(route('biaya.create'))
            ->assertSessionHasErrors('nominal');

        $this->from(route('biaya.create'))
            ->post(route('biaya.store'), $this->validBiayaData($foreignKebun))
            ->assertRedirect(route('biaya.create'))
            ->assertSessionHasErrors('kebun_id');

        $this->assertDatabaseCount('biayas', 0);
    }

    public function test_a_user_cannot_view_update_or_delete_another_users_biaya(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $kebun = $this->createKebun($owner, 'Kebun Pemilik');
        $biaya = $this->createBiaya($owner, $kebun, '2026-03-12', 'Biaya Rahasia');

        $this->actingAs($otherUser)
            ->get(route('biaya.index'))
            ->assertOk()
            ->assertDontSee('Biaya Rahasia');

        $this->get(route('biaya.edit', $biaya))->assertNotFound();
        $this->put(route('biaya.update', $biaya), $this->validBiayaData($kebun))->assertForbidden();
        $this->delete(route('biaya.destroy', $biaya))->assertNotFound();

        $this->assertDatabaseHas('biayas', ['id' => $biaya->id, 'keterangan' => 'Biaya Rahasia']);
    }

    public function test_cost_filters_apply_month_estate_and_category_and_keep_query_for_pagination(): void
    {
        $this->travelTo(Carbon::parse('2026-03-20 09:00:00'));
        $user = User::factory()->create();
        $kebunA = $this->createKebun($user, 'Kebun A');
        $kebunB = $this->createKebun($user, 'Kebun B');

        for ($index = 1; $index <= 11; $index++) {
            $this->createBiaya($user, $kebunA, sprintf('2026-03-%02d', $index), "Pupuk Maret {$index}", 'Pupuk');
        }
        $this->createBiaya($user, $kebunA, '2026-03-31', 'Pupuk Akhir Bulan', 'Pupuk');
        $this->createBiaya($user, $kebunB, '2026-03-14', 'Pupuk Kebun B', 'Pupuk');
        $this->createBiaya($user, $kebunA, '2026-03-15', 'Upah Kebun A', 'Upah panen');
        $this->createBiaya($user, $kebunA, '2026-02-14', 'Pupuk Februari', 'Pupuk');

        $response = $this->actingAs($user)
            ->get(route('biaya.index', [
                'bulan' => '2026-03',
                'kebun_id' => $kebunA->id,
                'kategori' => 'Pupuk',
            ]))
            ->assertOk()
            ->assertSee('Pupuk Maret 11')
            ->assertSee('Pupuk Akhir Bulan')
            ->assertDontSee('Pupuk Kebun B')
            ->assertDontSee('Upah Kebun A')
            ->assertDontSee('Pupuk Februari')
            ->assertSee('bulan=2026-03')
            ->assertSee('kebun_id='.$kebunA->id)
            ->assertSee('kategori=Pupuk')
            ->assertSee('page=2');

        $this->assertSame(12, $response->viewData('summary')['jumlah_catatan']);
        $this->assertEquals(12_000, $response->viewData('summary')['total_nominal']);
    }

    public function test_automatic_wage_is_calculated_from_owned_harvests_within_the_selected_range(): void
    {
        $this->travelTo(Carbon::parse('2026-03-20 09:00:00'));
        $user = User::factory()->create();
        $kebunA = $this->createKebun($user, 'Kebun A');
        $kebunB = $this->createKebun($user, 'Kebun B');
        $this->createPanen($user, $kebunA, '2026-03-02', 1200);
        $this->createPanen($user, $kebunA, '2026-03-20', 800);
        $this->createPanen($user, $kebunA, '2026-02-28', 5000);
        $this->createPanen($user, $kebunB, '2026-03-10', 9000);
        $this->assertTrue(Panen::query()
            ->where('user_id', $user->id)
            ->where('kebun_id', $kebunA->id)
            ->whereDate('tanggal', '2026-03-20')
            ->where('berat_kg', 800)
            ->exists());
        $otherUser = User::factory()->create();
        $otherKebun = $this->createKebun($otherUser, 'Kebun Orang Lain');
        $this->createPanen($otherUser, $otherKebun, '2026-03-10', 7000);

        $this->actingAs($user)
            ->post(route('biaya.store'), [
                ...$this->validBiayaData($kebunA),
                'kategori' => 'Upah panen',
                'mode_hitung' => 'otomatis',
                'nominal' => 1,
                'tarif_per_kg' => 250,
                'tanggal_mulai' => '2026-03-01',
                'tanggal_selesai' => '2026-03-20',
            ])
            ->assertRedirect(route('biaya.index'));

        $biaya = Biaya::query()->where('kategori', 'Upah panen')->firstOrFail();
        $this->assertSame('otomatis', $biaya->mode_hitung);
        $this->assertSame(2000.0, (float) $biaya->total_berat_kg);
        $this->assertSame(250.0, (float) $biaya->tarif_per_kg);
        $this->assertSame(500000.0, (float) $biaya->nominal);

        $this->get(route('biaya.preview', [
            'kebun_id' => $kebunA->id,
            'tarif_per_kg' => 250,
            'tanggal_mulai' => '2026-03-01',
            'tanggal_selesai' => '2026-03-20',
        ]))
            ->assertOk()
            ->assertJsonPath('has_harvest', true)
            ->assertJsonPath('total_berat_kg', 2000)
            ->assertJsonPath('nominal', 500000);
    }

    public function test_automatic_wage_is_not_saved_without_harvest_in_the_range(): void
    {
        $user = User::factory()->create();
        $kebun = $this->createKebun($user);

        $this->actingAs($user)
            ->from(route('biaya.create'))
            ->post(route('biaya.store'), [
                ...$this->validBiayaData($kebun),
                'kategori' => 'Upah panen',
                'mode_hitung' => 'otomatis',
                'nominal' => 999999,
                'tarif_per_kg' => 200,
                'tanggal_mulai' => '2026-03-01',
                'tanggal_selesai' => '2026-03-20',
            ])
            ->assertRedirect(route('biaya.create'))
            ->assertSessionHasErrors('tanggal_mulai');

        $this->assertDatabaseCount('biayas', 0);
    }

    public function test_dashboard_cost_charts_use_real_expense_records(): void
    {
        $this->travelTo(Carbon::parse('2026-03-31 09:00:00'));
        $user = User::factory()->create();
        $kebun = $this->createKebun($user);
        $this->createBiaya($user, $kebun, today()->toDateString(), 'Pupuk dashboard', 'Pupuk');
        $this->createBiaya($user, $kebun, today()->toDateString(), 'Upah dashboard', 'Upah panen');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="chart-fertilizer"', false)
            ->assertSee('id="chart-wages"', false)
            ->assertSee('id="chart-expenses"', false)
            ->assertDontSee('Belum ada data biaya');
    }

    public function test_create_page_asks_the_user_to_add_a_kebun_if_they_have_none(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('biaya.create'))
            ->assertOk()
            ->assertSee('Tambah kebun dulu')
            ->assertSee(route('kebun.create'));
    }

    public function test_database_seeder_still_creates_manual_and_automatic_cost_records(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('biayas', [
            'kategori' => 'Upah panen',
            'mode_hitung' => 'manual',
        ]);
        $this->assertDatabaseHas('biayas', [
            'kategori' => 'Upah panen',
            'mode_hitung' => 'otomatis',
            'tarif_per_kg' => 200,
            'total_berat_kg' => 1380,
            'nominal' => 276000,
        ]);
    }

    private function createKebun(User $user, string $name = 'Kebun Contoh'): Kebun
    {
        return $user->kebuns()->create([
            'nama' => $name,
            'lokasi' => 'Desa Contoh',
            'luas_ha' => 2.5,
        ]);
    }

    private function createBiaya(
        User $user,
        Kebun $kebun,
        string $date,
        string $description,
        string $category = 'Pupuk',
    ): Biaya {
        return $user->biayas()->create([
            'kebun_id' => $kebun->id,
            'tanggal' => $date,
            'kategori' => $category,
            'nominal' => 1000,
            'keterangan' => $description,
            'mode_hitung' => 'manual',
        ]);
    }

    private function createPanen(User $user, Kebun $kebun, string $date, float $weight): Panen
    {
        return $user->panens()->create([
            'kebun_id' => $kebun->id,
            'tanggal' => $date,
            'jumlah_tandan' => 80,
            'berat_kg' => $weight,
            'harga_per_kg' => 2500,
        ]);
    }

    /**
     * @return array<string, int|string>
     */
    private function validBiayaData(Kebun $kebun): array
    {
        return [
            'tanggal' => today()->toDateString(),
            'kebun_id' => $kebun->id,
            'kategori' => 'Pupuk',
            'nominal' => 450000,
            'keterangan' => 'Pupuk awal',
            'mode_hitung' => 'manual',
        ];
    }
}
