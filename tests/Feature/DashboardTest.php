<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_sample_data_and_only_lists_the_authenticated_users_kebun(): void
    {
        $user = User::factory()->create();
        $user->kebuns()->create([
            'nama' => 'Kebun Milik Saya',
            'lokasi' => 'Desa Contoh',
            'luas_ha' => 2.5,
        ]);

        $otherUser = User::factory()->create();
        $otherUser->kebuns()->create([
            'nama' => 'Kebun Milik Orang Lain',
            'lokasi' => 'Desa Lain',
            'luas_ha' => 8,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kebun Milik Saya')
            ->assertDontSee('Kebun Milik Orang Lain')
            ->assertSee('Total pendapatan bulan ini')
            ->assertSee('Data contoh')
            ->assertSee('Panen terbaru')
            ->assertSee('calendar-grid');
    }
}
