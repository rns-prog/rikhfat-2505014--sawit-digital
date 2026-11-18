<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Kebun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JadwalManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_update_and_delete_their_own_jadwal(): void
    {
        $user = User::factory()->create();
        $kebun = $this->createKebun($user, 'Kebun Saya');
        $this->actingAs($user);

        $this->post(route('jadwal.store'), $this->validData($kebun))
            ->assertRedirect(route('jadwal.index'))
            ->assertSessionHas('success', 'Jadwal berhasil ditambahkan.');

        $jadwal = Jadwal::query()->where('judul', 'Panen Blok A')->firstOrFail();
        $this->assertSame($user->id, $jadwal->user_id);
        $this->assertSame($kebun->id, $jadwal->kebun_id);

        $this->put(route('jadwal.update', $jadwal), [
            ...$this->validData($kebun),
            'jenis' => 'pupuk',
            'judul' => 'Pemupukan Blok A',
            'tanggal_mulai' => '2026-02-02',
            'ulang_setiap' => null,
            'ulang_satuan' => null,
        ])->assertRedirect(route('jadwal.index'))
            ->assertSessionHas('success', 'Jadwal berhasil diperbarui.');

        $this->assertDatabaseHas('jadwals', [
            'id' => $jadwal->id,
            'jenis' => 'pupuk',
            'judul' => 'Pemupukan Blok A',
            'ulang_setiap' => null,
            'ulang_satuan' => null,
        ]);
        $this->assertTrue($jadwal->fresh()->tanggal_mulai->isSameDay('2026-02-02'));

        $this->get(route('jadwal.index'))
            ->assertOk()
            ->assertSee('confirm(')
            ->assertSee('Hapus jadwal ini?');

        $this->delete(route('jadwal.destroy', $jadwal))
            ->assertRedirect(route('jadwal.index'))
            ->assertSessionHas('success', 'Jadwal berhasil dihapus.');

        $this->assertDatabaseMissing('jadwals', ['id' => $jadwal->id]);
    }

    public function test_schedule_validation_rejects_invalid_values_and_foreign_estate(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownKebun = $this->createKebun($user, 'Kebun Saya');
        $foreignKebun = $this->createKebun($otherUser, 'Kebun Orang Lain');

        $this->actingAs($user)
            ->from(route('jadwal.create'))
            ->post(route('jadwal.store'), [
                ...$this->validData($ownKebun),
                'ulang_setiap' => 0,
                'ulang_satuan' => null,
                'tanggal_berakhir' => '2025-01-01',
            ])
            ->assertRedirect(route('jadwal.create'))
            ->assertSessionHasErrors(['ulang_setiap', 'tanggal_berakhir']);

        $this->from(route('jadwal.create'))
            ->post(route('jadwal.store'), [
                ...$this->validData($ownKebun),
                'ulang_setiap' => null,
                'ulang_satuan' => 'minggu',
            ])
            ->assertRedirect(route('jadwal.create'))
            ->assertSessionHasErrors('ulang_setiap');

        $this->from(route('jadwal.create'))
            ->post(route('jadwal.store'), $this->validData($foreignKebun))
            ->assertRedirect(route('jadwal.create'))
            ->assertSessionHasErrors('kebun_id');

        $this->assertDatabaseCount('jadwals', 0);
    }

    public function test_a_user_cannot_access_another_users_schedule(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $kebun = $this->createKebun($owner, 'Kebun Pemilik');
        $jadwal = $owner->jadwals()->create($this->validData($kebun));

        $this->actingAs($otherUser)
            ->get(route('jadwal.index'))
            ->assertOk()
            ->assertDontSee('Panen Blok A');

        $this->get(route('jadwal.edit', $jadwal))->assertNotFound();
        $this->put(route('jadwal.update', $jadwal), $this->validData($kebun))->assertForbidden();
        $this->delete(route('jadwal.destroy', $jadwal))->assertNotFound();
        $this->assertDatabaseHas('jadwals', ['id' => $jadwal->id, 'user_id' => $owner->id]);
    }

    public function test_dashboard_calendar_and_upcoming_events_use_only_the_users_schedules(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 10:00:00'));
        $user = User::factory()->create();
        $kebun = $this->createKebun($user, 'Kebun Saya');
        $user->jadwals()->create([
            'kebun_id' => $kebun->id,
            'jenis' => 'panen',
            'judul' => 'Panen Mingguan',
            'tanggal_mulai' => '2026-03-12',
            'ulang_setiap' => 1,
            'ulang_satuan' => 'minggu',
        ]);
        $user->jadwals()->create([
            'jenis' => 'pupuk',
            'judul' => 'Pemupukan Hari Ini',
            'tanggal_mulai' => '2026-03-10',
        ]);
        $otherUser = User::factory()->create();
        $otherKebun = $this->createKebun($otherUser, 'Kebun Lain');
        $otherUser->jadwals()->create([
            'kebun_id' => $otherKebun->id,
            'jenis' => 'panen',
            'judul' => 'Jadwal Rahasia',
            'tanggal_mulai' => '2026-03-11',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pemupukan Hari Ini')
            ->assertSee('Panen Mingguan')
            ->assertDontSee('Jadwal Rahasia')
            ->assertSee('data-calendar-events-url="'.route('jadwal.calendar-events').'"', false)
            ->assertSee('Hari ini');

        $this->get(route('jadwal.calendar-events', ['bulan' => '2026-03']))
            ->assertOk()
            ->assertJsonCount(4, 'events')
            ->assertJsonFragment(['title' => 'Pemupukan Hari Ini'])
            ->assertJsonFragment(['title' => 'Panen Mingguan'])
            ->assertJsonMissing(['title' => 'Jadwal Rahasia']);
    }

    public function test_dashboard_renders_without_any_schedule(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada jadwal')
            ->assertSee('data-calendar-events="[]"', false);
    }

    public function test_guests_are_redirected_to_login_for_schedule_routes(): void
    {
        $this->get(route('jadwal.index'))->assertRedirect(route('login'));
        $this->get(route('jadwal.calendar-events', ['bulan' => '2026-03']))
            ->assertRedirect(route('login'));
    }

    private function createKebun(User $user, string $name): Kebun
    {
        return $user->kebuns()->create([
            'nama' => $name,
            'lokasi' => 'Desa Contoh',
            'luas_ha' => 2.5,
        ]);
    }

    /**
     * @return array<string, int|string|null>
     */
    private function validData(Kebun $kebun): array
    {
        return [
            'jenis' => 'panen',
            'judul' => 'Panen Blok A',
            'kebun_id' => $kebun->id,
            'tanggal_mulai' => '2026-01-05',
            'ulang_setiap' => 2,
            'ulang_satuan' => 'minggu',
            'tanggal_berakhir' => '2026-03-31',
            'catatan' => 'Periksa jalur kebun',
        ];
    }
}
