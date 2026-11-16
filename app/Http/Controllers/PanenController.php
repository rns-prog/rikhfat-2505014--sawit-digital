<?php

namespace App\Http\Controllers;

use App\Http\Requests\PanenRequest;
use App\Models\Panen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PanenController extends Controller
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
        ]);

        $month = $filters['bulan'] ?? today()->format('Y-m');
        $selectedKebun = $filters['kebun_id'] ?? null;
        $periodStart = Carbon::createFromFormat('!Y-m', $month)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $panenQuery = $request->user()->panens()
            ->with('kebun')
            ->whereBetween('tanggal', [$periodStart->toDateString(), $periodEnd->toDateString()]);

        if ($selectedKebun !== null) {
            $panenQuery->where('kebun_id', $selectedKebun);
        }

        $summary = (clone $panenQuery)->toBase()
            ->selectRaw('COALESCE(SUM(berat_kg), 0) as total_berat')
            ->selectRaw('COALESCE(SUM(berat_kg * harga_per_kg), 0) as total_pendapatan')
            ->selectRaw('COUNT(*) as jumlah_catatan')
            ->first();

        return view('panen.index', [
            'panens' => $panenQuery->latest('tanggal')->latest('id')->paginate(10)->withQueryString(),
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
            'bulan' => $month,
            'selectedKebun' => $selectedKebun,
            'summary' => $summary,
        ]);
    }

    public function create(Request $request): View
    {
        return view('panen.create', [
            'panen' => new Panen(['tanggal' => today()->toDateString()]),
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
        ]);
    }

    public function store(PanenRequest $request): RedirectResponse
    {
        $request->user()->panens()->create($request->validated());

        return redirect()->route('panen.index')->with('success', 'Catatan panen berhasil ditambahkan.');
    }

    public function edit(Request $request, Panen $panen): View
    {
        $milikPengguna = $request->user()->panens()->findOrFail($panen->getKey());

        return view('panen.edit', [
            'panen' => $milikPengguna,
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
        ]);
    }

    public function update(PanenRequest $request, Panen $panen): RedirectResponse
    {
        $milikPengguna = $request->user()->panens()->findOrFail($panen->getKey());
        $milikPengguna->update($request->validated());

        return redirect()->route('panen.index')->with('success', 'Catatan panen berhasil diperbarui.');
    }

    public function destroy(Request $request, Panen $panen): RedirectResponse
    {
        $milikPengguna = $request->user()->panens()->findOrFail($panen->getKey());
        $milikPengguna->delete();

        return redirect()->route('panen.index')->with('success', 'Catatan panen berhasil dihapus.');
    }
}
