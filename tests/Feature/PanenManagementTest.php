<?php

namespace Tests\Feature;

use App\Models\Kebun;
use App\Models\Panen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PanenManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_update_and_delete_their_own_panen(): void
    {
        $user = User::factory()->create();
        $kebun = $this->createKebun($user, 'Kebun Saya');
        $this->actingAs($user);

        $this->post(route('panen.store'), $this->validPanenData($kebun))
            ->assertRedirect(route('panen.index'))
            ->assertSessionHas('success', 'Catatan panen berhasil ditambahkan.');

        $panen = Panen::query()->where('pembeli', 'Pembeli Awal')->firstOrFail();
        $this->assertSame($user->id, $panen->user_id);
        $this->assertSame($kebun->id, $panen->kebun_id);
        $this->assertEquals(3_562_500, $panen->pendapatan);

        $this->put(route('panen.update', $panen), [
            ...$this->validPanenData($kebun),
            'berat_kg' => 1500,
            'harga_per_kg' => 3000,
            'pembeli' => 'Pembeli Baru',
        ])->assertRedirect(route('panen.index'))
            ->assertSessionHas('success', 'Catatan panen berhasil diperbarui.');

        $this->assertDatabaseHas('panens', [
            'id' => $panen->id,
            'berat_kg' => 1500,
            'harga_per_kg' => 3000,
            'pembeli' => 'Pembeli Baru',
        ]);
        $this->assertEquals(4_500_000, $panen->fresh()->pendapatan);

        $this->from(route('panen.index'))
            ->get(route('panen.index'))
            ->assertSee('confirm(')
            ->assertSee('Hapus catatan panen ini?');

        $this->delete(route('panen.destroy', $panen))
            ->assertRedirect(route('panen.index'))
            ->assertSessionHas('success', 'Catatan panen berhasil dihapus.');

        $this->assertDatabaseMissing('panens', ['id' => $panen->id]);
    }

    public function test_harvest_validation_rejects_negative_weight_and_price(): void
    {
        $user = User::factory()->create();
        $kebun = $this->createKebun($user);

        $this->actingAs($user)
            ->from(route('panen.create'))
            ->post(route('panen.store'), [
                ...$this->validPanenData($kebun),
                'berat_kg' => -10,
                'harga_per_kg' => -100,
            ])
            ->assertRedirect(route('panen.create'))
            ->assertSessionHasErrors(['berat_kg', 'harga_per_kg']);

        $this->assertDatabaseCount('panens', 0);
    }

    public function test_a_user_cannot_choose_another_users_kebun(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherKebun = $this->createKebun($otherUser, 'Kebun Orang Lain');

        $this->actingAs($user)
            ->from(route('panen.create'))
            ->post(route('panen.store'), $this->validPanenData($otherKebun))
            ->assertRedirect(route('panen.create'))
            ->assertSessionHasErrors('kebun_id');

        $this->assertDatabaseCount('panens', 0);
    }

    public function test_a_user_cannot_view_update_or_delete_another_users_panen(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $kebun = $this->createKebun($owner, 'Kebun Pemilik');
        $panen = $this->createPanen($owner, $kebun, '2026-03-12', 'Rahasia Pembeli');

        $this->actingAs($otherUser)
            ->get(route('panen.index'))
            ->assertOk()
            ->assertDontSee('Rahasia Pembeli');

        $this->get(route('panen.edit', $panen))->assertNotFound();
        $this->put(route('panen.update', $panen), $this->validPanenData($kebun))->assertForbidden();
        $this->delete(route('panen.destroy', $panen))->assertNotFound();

        $this->assertDatabaseHas('panens', ['id' => $panen->id, 'pembeli' => 'Rahasia Pembeli']);
    }

    public function test_harvest_filters_limit_by_month_and_kebun_and_pagination_keeps_filters(): void
    {
        $this->travelTo(Carbon::parse('2026-03-20 09:00:00'));
        $user = User::factory()->create();
        $kebunA = $this->createKebun($user, 'Kebun A');
        $kebunB = $this->createKebun($user, 'Kebun B');

        for ($index = 1; $index <= 11; $index++) {
            $date = sprintf('2026-03-%02d', $index);
            $this->createPanen($user, $kebunA, $date, "Pembeli Maret {$index}");
        }
        $this->createPanen($user, $kebunB, '2026-03-14', 'Pembeli Kebun B');
        $this->createPanen($user, $kebunA, '2026-02-14', 'Pembeli Februari');

        $response = $this->actingAs($user)
            ->get(route('panen.index', ['bulan' => '2026-03', 'kebun_id' => $kebunA->id]))
            ->assertOk()
            ->assertSee('Pembeli Maret 11')
            ->assertDontSee('Pembeli Kebun B')
            ->assertDontSee('Pembeli Februari')
            ->assertSee('bulan=2026-03')
            ->assertSee('kebun_id='.$kebunA->id)
            ->assertSee('page=2');

        $this->assertSame(11, $response->viewData('summary')->jumlah_catatan);
        $this->assertEquals(11_000, (float) $response->viewData('summary')->total_berat);

    }

    public function test_create_page_asks_the_user_to_add_a_kebun_if_they_have_none(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('panen.create'))
            ->assertOk()
            ->assertSee('Tambah kebun dulu')
            ->assertSee(route('kebun.create'));
    }

    private function createKebun(User $user, string $name = 'Kebun Contoh'): Kebun
    {
        return $user->kebuns()->create([
            'nama' => $name,
            'lokasi' => 'Desa Contoh',
            'luas_ha' => 2.5,
        ]);
    }

    private function createPanen(User $user, Kebun $kebun, string $date, string $buyer): Panen
    {
        return $user->panens()->create([
            'kebun_id' => $kebun->id,
            'tanggal' => $date,
            'jumlah_tandan' => 80,
            'berat_kg' => 1000,
            'harga_per_kg' => 2500,
            'pembeli' => $buyer,
        ]);
    }

    /**
     * @return array<string, int|string>
     */
    private function validPanenData(Kebun $kebun): array
    {
        return [
            'tanggal' => today()->toDateString(),
            'kebun_id' => $kebun->id,
            'jumlah_tandan' => 85,
            'berat_kg' => 1250,
            'harga_per_kg' => 2850,
            'pembeli' => 'Pembeli Awal',
        ];
    }
}
