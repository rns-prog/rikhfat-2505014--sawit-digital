<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_the_authenticated_users_harvest_revenue_and_weight(): void
    {
        $user = User::factory()->create();
        $kebun = $user->kebuns()->create([
            'nama' => 'Kebun Saya',
            'lokasi' => 'Desa Contoh',
            'luas_ha' => 2.5,
        ]);
        $user->panens()->create([
            'kebun_id' => $kebun->id,
            'tanggal' => now()->startOfMonth()->addDays(2)->toDateString(),
            'jumlah_tandan' => 120,
            'berat_kg' => 1500,
            'harga_per_kg' => 2000,
            'pembeli' => 'Pengepul Lokal',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 3.000.000')
            ->assertSee('1.500 kg')
            ->assertSee('Kebun Saya')
            ->assertSee('Panen terbaru')
            ->assertSee('Belum ada pembanding')
            ->assertDontSee('Data contoh');
    }

    public function test_dashboard_does_not_include_another_users_harvests(): void
    {
        $user = User::factory()->create();
        $ownKebun = $user->kebuns()->create([
            'nama' => 'Kebun Milik Saya',
            'lokasi' => 'Desa Saya',
            'luas_ha' => 2,
        ]);
        $user->panens()->create([
            'kebun_id' => $ownKebun->id,
            'tanggal' => now()->startOfMonth()->addDays(2)->toDateString(),
            'jumlah_tandan' => 50,
            'berat_kg' => 100,
            'harga_per_kg' => 1000,
            'pembeli' => 'Pembeli Saya',
        ]);

        $otherUser = User::factory()->create();
        $otherKebun = $otherUser->kebuns()->create([
            'nama' => 'Kebun Milik Orang Lain',
            'lokasi' => 'Desa Lain',
            'luas_ha' => 8,
        ]);
        $otherUser->panens()->create([
            'kebun_id' => $otherKebun->id,
            'tanggal' => now()->startOfMonth()->addDays(2)->toDateString(),
            'jumlah_tandan' => 900,
            'berat_kg' => 90000,
            'harga_per_kg' => 90000,
            'pembeli' => 'Pembeli Lain',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 100.000')
            ->assertSee('100 kg')
            ->assertDontSee('Kebun Milik Orang Lain')
            ->assertDontSee('Pembeli Lain')
            ->assertDontSee('90.000 kg');
    }

    public function test_dashboard_renders_when_the_users_harvest_and_expense_tables_are_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum ada data pendapatan')
            ->assertSee('Belum ada data biaya')
            ->assertSee('Belum ada catatan panen')
            ->assertSee('Belum ada jadwal')
            ->assertSee('data-calendar-events="[]"', false);
    }
}
