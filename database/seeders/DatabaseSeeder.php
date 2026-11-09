<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'petani.contoh@example.com'],
            [
                'name' => 'Petani Contoh',
                'password' => Hash::make('password'),
            ],
        );

        $kebunUtara = $user->kebuns()->updateOrCreate(
            ['nama' => 'Kebun Utara'],
            ['lokasi' => 'Desa Suka Makmur', 'luas_ha' => 3.50],
        );

        $kebunSelatan = $user->kebuns()->updateOrCreate(
            ['nama' => 'Kebun Selatan'],
            ['lokasi' => 'Desa Harapan Jaya', 'luas_ha' => 5.25],
        );

        $panen = [
            [$kebunUtara, '2026-02-10', 85, 1250.00, 2850.00, 'Koperasi Sawit Makmur'],
            [$kebunUtara, '2026-03-12', 92, 1380.00, 2900.00, 'Koperasi Sawit Makmur'],
            [$kebunSelatan, '2026-03-15', 120, 1820.00, 2900.00, 'Tengkulak Pak Budi'],
            [$kebunSelatan, '2026-04-18', 128, 1950.00, 3000.00, 'Tengkulak Pak Budi'],
        ];

        foreach ($panen as [$kebun, $tanggal, $jumlahTandan, $beratKg, $hargaPerKg, $pembeli]) {
            $catatanPanen = $kebun->panens()
                ->whereDate('tanggal', $tanggal)
                ->first() ?? $kebun->panens()->make();
            $catatanPanen->fill([
                'user_id' => $user->id,
                'tanggal' => $tanggal,
                'jumlah_tandan' => $jumlahTandan,
                'berat_kg' => $beratKg,
                'harga_per_kg' => $hargaPerKg,
                'pembeli' => $pembeli,
            ])->save();
        }

        $biayas = [
            [$kebunUtara, '2026-02-10', 'Upah panen', 275000.00, 'Upah panen Februari', 'manual', null, null, null, null],
            [$kebunUtara, '2026-03-12', 'Upah panen', 276000.00, 'Upah berdasarkan berat panen Maret', 'otomatis', 200.00, '2026-03-01', '2026-03-31', 1380.00],
            [$kebunUtara, '2026-02-05', 'Pupuk', 450000.00, 'Pembelian pupuk NPK', 'manual', null, null, null, null],
            [$kebunSelatan, '2026-03-05', 'Perawatan', 350000.00, 'Pembersihan jalur kebun', 'manual', null, null, null, null],
            [$kebunSelatan, '2026-04-18', 'Transportasi', 180000.00, 'Angkut hasil panen', 'manual', null, null, null, null],
        ];

        foreach ($biayas as [$kebun, $tanggal, $kategori, $nominal, $keterangan, $modeHitung, $tarif, $mulai, $selesai, $totalBerat]) {
            $catatanBiaya = $kebun->biayas()
                ->whereDate('tanggal', $tanggal)
                ->where('kategori', $kategori)
                ->where('mode_hitung', $modeHitung)
                ->first() ?? $kebun->biayas()->make();
            $catatanBiaya->fill([
                'user_id' => $user->id,
                'tanggal' => $tanggal,
                'kategori' => $kategori,
                'nominal' => $nominal,
                'keterangan' => $keterangan,
                'mode_hitung' => $modeHitung,
                'tarif_per_kg' => $tarif,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
                'total_berat_kg' => $totalBerat,
            ])->save();
        }
    }
}
