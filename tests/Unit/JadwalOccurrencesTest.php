<?php

namespace Tests\Unit;

use App\Models\Jadwal;
use Tests\TestCase;

class JadwalOccurrencesTest extends TestCase
{
    public function test_one_time_schedule_occurs_only_on_its_start_date(): void
    {
        $jadwal = $this->schedule('2026-03-10');

        $this->assertSame(
            ['2026-03-10'],
            $jadwal->occurrencesBetween('2026-03-01', '2026-03-31')->map->toDateString()->all(),
        );
        $this->assertCount(0, $jadwal->occurrencesBetween('2026-04-01', '2026-04-30'));
    }

    public function test_recurring_schedule_occurs_every_n_weeks(): void
    {
        $jadwal = $this->schedule('2026-01-05', 2, 'minggu');

        $this->assertSame(
            ['2026-01-05', '2026-01-19', '2026-02-02'],
            $jadwal->occurrencesBetween('2026-01-01', '2026-02-02')->map->toDateString()->all(),
        );
    }

    public function test_recurring_schedule_occurs_every_n_months_from_the_original_start_date(): void
    {
        $jadwal = $this->schedule('2026-01-31', 2, 'bulan');

        $this->assertSame(
            ['2026-01-31', '2026-03-31', '2026-05-31'],
            $jadwal->occurrencesBetween('2026-01-01', '2026-05-31')->map->toDateString()->all(),
        );
    }

    public function test_recurrence_stops_at_the_end_date_inclusively(): void
    {
        $jadwal = $this->schedule('2026-01-05', 1, 'minggu', '2026-01-19');

        $this->assertSame(
            ['2026-01-05', '2026-01-12', '2026-01-19'],
            $jadwal->occurrencesBetween('2026-01-01', '2026-02-01')->map->toDateString()->all(),
        );
    }

    public function test_date_range_without_any_occurrence_returns_an_empty_collection(): void
    {
        $jadwal = $this->schedule('2026-01-05', 2, 'minggu');

        $this->assertCount(0, $jadwal->occurrencesBetween('2026-01-06', '2026-01-18'));
        $this->assertCount(0, $jadwal->occurrencesBetween('2026-02-01', '2026-01-01'));
    }

    private function schedule(
        string $start,
        ?int $repeat = null,
        ?string $unit = null,
        ?string $end = null,
    ): Jadwal {
        return new Jadwal([
            'jenis' => Jadwal::JENIS_PANEN,
            'judul' => 'Panen',
            'tanggal_mulai' => $start,
            'ulang_setiap' => $repeat,
            'ulang_satuan' => $unit,
            'tanggal_berakhir' => $end,
        ]);
    }
}
