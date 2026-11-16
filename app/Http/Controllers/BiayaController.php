<?php

namespace App\Http\Controllers;

use App\Http\Requests\BiayaRequest;
use App\Models\Biaya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BiayaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'bulan' => ['nullable', 'date_format:Y-m'],
            'kebun_id' => [
                'nullable',
                'integer',
                Rule::exists('kebuns', 'id')->where('user_id', $request->user()->id),
            ],
            'kategori' => ['nullable', 'string', Rule::in(Biaya::KATEGORI)],
        ]);

        $month = $filters['bulan'] ?? today()->format('Y-m');
        $selectedKebun = $filters['kebun_id'] ?? null;
        $selectedKategori = $filters['kategori'] ?? null;
        $periodStart = Carbon::createFromFormat('!Y-m', $month)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $biayaQuery = $request->user()->biayas()
            ->with('kebun')
            ->whereDate('tanggal', '>=', $periodStart->toDateString())
            ->whereDate('tanggal', '<=', $periodEnd->toDateString());

        if ($selectedKebun !== null) {
            $biayaQuery->where('kebun_id', $selectedKebun);
        }

        if ($selectedKategori !== null) {
            $biayaQuery->where('kategori', $selectedKategori);
        }

        $summaryTotals = (clone $biayaQuery)->toBase()
            ->selectRaw('COALESCE(SUM(nominal), 0) as total_nominal')
            ->selectRaw('COUNT(*) as jumlah_catatan')
            ->first();
        $totalsByCategory = (clone $biayaQuery)->toBase()
            ->select('kategori')
            ->selectRaw('COALESCE(SUM(nominal), 0) as total_nominal')
            ->groupBy('kategori')
            ->pluck('total_nominal', 'kategori');
        $categoryTotals = collect(Biaya::KATEGORI)
            ->mapWithKeys(fn (string $category): array => [
                $category => (float) ($totalsByCategory[$category] ?? 0),
            ]);
        $summary = [
            'total_nominal' => (float) $summaryTotals->total_nominal,
            'jumlah_catatan' => (int) $summaryTotals->jumlah_catatan,
            'kategori' => $categoryTotals,
        ];

        return view('biaya.index', [
            'biayas' => $biayaQuery->latest('tanggal')->latest('id')->paginate(10)->withQueryString(),
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
            'bulan' => $month,
            'selectedKebun' => $selectedKebun,
            'selectedKategori' => $selectedKategori,
            'kategoriBiaya' => Biaya::KATEGORI,
            'summary' => $summary,
        ]);
    }

    public function create(Request $request): View
    {
        return view('biaya.create', [
            'biaya' => new Biaya(['tanggal' => today()->toDateString(), 'mode_hitung' => Biaya::MODE_MANUAL]),
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
            'kategoriBiaya' => Biaya::KATEGORI,
        ]);
    }

    public function store(BiayaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $computed = $this->automaticWageData($request, $data);

        if ($computed === null) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Tidak ada catatan panen pada kebun dan rentang tanggal tersebut.',
            ]);
        }

        $request->user()->biayas()->create($computed);

        return redirect()->route('biaya.index')->with('success', 'Catatan biaya berhasil ditambahkan.');
    }

    public function edit(Request $request, Biaya $biaya): View
    {
        $milikPengguna = $request->user()->biayas()->findOrFail($biaya->getKey());

        return view('biaya.edit', [
            'biaya' => $milikPengguna,
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
            'kategoriBiaya' => Biaya::KATEGORI,
        ]);
    }

    public function update(BiayaRequest $request, Biaya $biaya): RedirectResponse
    {
        $milikPengguna = $request->user()->biayas()->findOrFail($biaya->getKey());
        $data = $request->validated();
        $computed = $this->automaticWageData($request, $data);

        if ($computed === null) {
            return back()->withInput()->withErrors([
                'tanggal_mulai' => 'Tidak ada catatan panen pada kebun dan rentang tanggal tersebut.',
            ]);
        }

        $milikPengguna->update($computed);

        return redirect()->route('biaya.index')->with('success', 'Catatan biaya berhasil diperbarui.');
    }

    public function destroy(Request $request, Biaya $biaya): RedirectResponse
    {
        $milikPengguna = $request->user()->biayas()->findOrFail($biaya->getKey());
        $milikPengguna->delete();

        return redirect()->route('biaya.index')->with('success', 'Catatan biaya berhasil dihapus.');
    }

    public function previewUpah(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kebun_id' => [
                'required',
                'integer',
                Rule::exists('kebuns', 'id')->where('user_id', $request->user()->id),
            ],
            'tarif_per_kg' => ['required', 'numeric', 'gt:0'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ], [
            'kebun_id.exists' => 'Kebun yang dipilih tidak tersedia.',
            'tarif_per_kg.required' => 'Tarif per kg wajib diisi.',
            'tarif_per_kg.numeric' => 'Tarif per kg harus berupa angka.',
            'tarif_per_kg.gt' => 'Tarif per kg harus lebih besar dari 0.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $totalBerat = (float) $request->user()->panens()
            ->where('kebun_id', $data['kebun_id'])
            ->whereDate('tanggal', '>=', $data['tanggal_mulai'])
            ->whereDate('tanggal', '<=', $data['tanggal_selesai'])
            ->sum('berat_kg');
        $nominal = round($totalBerat * (float) $data['tarif_per_kg'], 2);

        return response()->json([
            'has_harvest' => $totalBerat > 0,
            'total_berat_kg' => $totalBerat,
            'tarif_per_kg' => (float) $data['tarif_per_kg'],
            'nominal' => $nominal,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    private function automaticWageData(Request $request, array $data): ?array
    {
        $payload = [
            'kebun_id' => $data['kebun_id'],
            'tanggal' => $data['tanggal'],
            'kategori' => $data['kategori'],
            'nominal' => $data['nominal'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
            'mode_hitung' => Biaya::MODE_MANUAL,
            'tarif_per_kg' => null,
            'tanggal_mulai' => null,
            'tanggal_selesai' => null,
            'total_berat_kg' => null,
        ];

        if (
            $data['kategori'] !== 'Upah panen'
            || ($data['mode_hitung'] ?? Biaya::MODE_MANUAL) !== Biaya::MODE_OTOMATIS
        ) {
            return $payload;
        }

        $totalBerat = (float) $request->user()->panens()
            ->where('kebun_id', $data['kebun_id'])
            ->whereDate('tanggal', '>=', $data['tanggal_mulai'])
            ->whereDate('tanggal', '<=', $data['tanggal_selesai'])
            ->sum('berat_kg');

        if ($totalBerat <= 0) {
            return null;
        }

        $tarif = (float) $data['tarif_per_kg'];
        $payload['mode_hitung'] = Biaya::MODE_OTOMATIS;
        $payload['tarif_per_kg'] = $tarif;
        $payload['tanggal_mulai'] = $data['tanggal_mulai'];
        $payload['tanggal_selesai'] = $data['tanggal_selesai'];
        $payload['total_berat_kg'] = $totalBerat;
        $payload['nominal'] = round($totalBerat * $tarif, 2);

        return $payload;
    }
}
