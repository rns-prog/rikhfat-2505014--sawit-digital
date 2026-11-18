<?php

namespace App\Http\Controllers;

use App\Http\Requests\JadwalRequest;
use App\Models\Jadwal;
use App\Services\JadwalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function __construct(private readonly JadwalService $jadwalService) {}

    public function index(Request $request): View
    {
        $jadwals = $request->user()->jadwals()
            ->with('kebun:id,nama')
            ->orderBy('tanggal_mulai')
            ->orderBy('judul')
            ->get();

        $jadwals->each(function (Jadwal $jadwal): void {
            $jadwal->setAttribute('tanggal_berikutnya', $jadwal->nextOccurrenceOnOrAfter(today()));
        });

        return view('jadwal.index', compact('jadwals'));
    }

    public function create(Request $request): View
    {
        return view('jadwal.create', [
            'jadwal' => new Jadwal([
                'jenis' => Jadwal::JENIS_PANEN,
                'judul' => 'Panen',
                'tanggal_mulai' => today()->toDateString(),
            ]),
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
        ]);
    }

    public function store(JadwalRequest $request): RedirectResponse
    {
        $request->user()->jadwals()->create($request->validated());

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Request $request, Jadwal $jadwal): View
    {
        $milikPengguna = $request->user()->jadwals()->findOrFail($jadwal->getKey());

        return view('jadwal.edit', [
            'jadwal' => $milikPengguna,
            'kebuns' => $request->user()->kebuns()->orderBy('nama')->get(),
        ]);
    }

    public function update(JadwalRequest $request, Jadwal $jadwal): RedirectResponse
    {
        $milikPengguna = $request->user()->jadwals()->findOrFail($jadwal->getKey());
        $milikPengguna->update($request->validated());

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $milikPengguna = $request->user()->jadwals()->findOrFail($jadwal->getKey());
        $milikPengguna->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'bulan' => ['required', 'date_format:Y-m'],
        ], [
            'bulan.required' => 'Pilih bulan kalender.',
            'bulan.date_format' => 'Format bulan kalender tidak valid.',
        ]);
        $month = Carbon::createFromFormat('!Y-m', $filters['bulan']);

        return response()->json([
            'events' => $this->jadwalService->eventsForRange(
                $request->user(),
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth(),
            ),
        ]);
    }
}
