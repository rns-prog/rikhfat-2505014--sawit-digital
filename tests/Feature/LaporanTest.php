<?php

namespace Tests\Feature;

use App\Models\Biaya;
use App\Models\Kebun;
use App\Models\Panen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_totals_include_only_the_authenticated_users_income_expenses_and_profit(): void
    {
        $this->travelTo(Carbon::parse('2026-11-16 10:00:00'));
        $user = User::factory()->create();
        $kebun = $this->createKebun($user, 'Kebun Saya');
        $this->createPanen($user, $kebun, '2026-03-10', 1000, 3000);
        $this->createBiaya($user, $kebun, '2026-03-12', 500000, 'Pupuk');

        $otherUser = User::factory()->create();
        $otherKebun = $this->createKebun($otherUser, 'Kebun Orang Lain');
        $this->createPanen($otherUser, $otherKebun, '2026-03-10', 90000, 90000);
        $this->createBiaya($otherUser, $otherKebun, '2026-03-12', 9000000, 'Pupuk');

        $response = $this->actingAs($user)
            ->get(route('laporan', ['tahun' => 2026]))
            ->assertOk()
            ->assertSee('Rp 3.000.000')
            ->assertSee('Rp 500.000')
            ->assertSee('Rp 2.500.000')
            ->assertSee('Kebun Saya')
            ->assertDontSee('Kebun Orang Lain');

        $this->assertSame(1000.0, $response->viewData('report')['summary']['panen_kg']);
        $this->assertSame(3000000.0, $response->viewData('report')['summary']['pendapatan']);
        $this->assertSame(500000.0, $response->viewData('report')['summary']['biaya']);
        $this->assertSame(2500000.0, $response->viewData('report')['summary']['laba']);
    }

    public function test_report_year_and_estate_filters_limit_period_and_data(): void
    {
        $this->travelTo(Carbon::parse('2026-11-16 10:00:00'));
        $user = User::factory()->create();
        $kebunA = $this->createKebun($user, 'Kebun A');
        $kebunB = $this->createKebun($user, 'Kebun B');
        $this->createPanen($user, $kebunA, '2025-03-10', 1000, 3000);
        $this->createBiaya($user, $kebunA, '2025-03-12', 500000, 'Pupuk');
        $this->createPanen($user, $kebunA, '2026-03-10', 2000, 3000);
        $this->createBiaya($user, $kebunA, '2026-03-12', 700000, 'Pupuk');
        $this->createPanen($user, $kebunB, '2026-03-10', 500, 3000);
        $this->createBiaya($user, $kebunB, '2026-03-12', 200000, 'Upah panen');

        $response = $this->actingAs($user)
            ->get(route('laporan', [
                'tahun' => 2026,
                'mode' => 'tahun',
                'kebun_id' => $kebunA->id,
            ]))
            ->assertOk()
            ->assertSee('Kebun A')
            ->assertSee('Per tahun')
            ->assertSee('Rp 6.000.000')
            ->assertSee('Rp 700.000')
            ->assertSee('Rp 5.300.000');

        $report = $response->viewData('report');
        $this->assertSame('Kebun A', $report['selectedKebun']->nama);
        $this->assertSame(1, $report['periodRows']->count());
        $this->assertSame(2000.0, $report['summary']['panen_kg']);
        $this->assertSame(5300000.0, $report['summary']['laba']);
        $this->assertCount(1, $report['estateRows']);
    }

    public function test_report_renders_empty_data_with_zero_months_and_no_errors(): void
    {
        $this->travelTo(Carbon::parse('2026-11-16 10:00:00'));
        $user = User::factory()->create();
        $this->createKebun($user, 'Kebun Kosong');

        $this->actingAs($user)
            ->get(route('laporan'))
            ->assertOk()
            ->assertSee('Belum ada data panen atau biaya')
            ->assertSee('Januari')
            ->assertSee('Desember')
            ->assertSee('Belum ada data untuk ditampilkan')
            ->assertSee('Rp 0');
    }

    public function test_report_pdf_download_returns_a_pdf(): void
    {
        $this->travelTo(Carbon::parse('2026-11-16 10:00:00'));
        $user = User::factory()->create();
        $kebun = $this->createKebun($user, 'Kebun PDF');
        $this->createPanen($user, $kebun, '2026-03-10', 1000, 3000);
        $this->createBiaya($user, $kebun, '2026-03-12', 500000, 'Pupuk');

        $this->actingAs($user)
            ->get(route('laporan.pdf', ['tahun' => 2026, 'kebun_id' => $kebun->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=laporan-sawitku-2026.pdf');
    }

    public function test_guests_are_redirected_to_login_for_report_and_pdf(): void
    {
        $this->get(route('laporan'))->assertRedirect(route('login'));
        $this->get(route('laporan.pdf'))->assertRedirect(route('login'));
    }

    private function createKebun(User $user, string $name): Kebun
    {
        return $user->kebuns()->create([
            'nama' => $name,
            'lokasi' => 'Desa Contoh',
            'luas_ha' => 2.5,
        ]);
    }

    private function createPanen(User $user, Kebun $kebun, string $date, float $weight, float $price): Panen
    {
        return $user->panens()->create([
            'kebun_id' => $kebun->id,
            'tanggal' => $date,
            'jumlah_tandan' => 80,
            'berat_kg' => $weight,
            'harga_per_kg' => $price,
        ]);
    }

    private function createBiaya(User $user, Kebun $kebun, string $date, float $amount, string $category): Biaya
    {
        return $user->biayas()->create([
            'kebun_id' => $kebun->id,
            'tanggal' => $date,
            'kategori' => $category,
            'nominal' => $amount,
            'mode_hitung' => 'manual',
        ]);
    }
}
