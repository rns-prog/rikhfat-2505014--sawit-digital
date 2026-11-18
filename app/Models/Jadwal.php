<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Jadwal extends Model
{
    public const JENIS_PANEN = 'panen';

    public const JENIS_PUPUK = 'pupuk';

    public const JENIS = [
        self::JENIS_PANEN,
        self::JENIS_PUPUK,
    ];

    public const SATUAN_MINGGU = 'minggu';

    public const SATUAN_BULAN = 'bulan';

    public const SATUAN_ULANG = [
        self::SATUAN_MINGGU,
        self::SATUAN_BULAN,
    ];

    protected $fillable = [
        'user_id',
        'kebun_id',
        'jenis',
        'judul',
        'tanggal_mulai',
        'ulang_setiap',
        'ulang_satuan',
        'tanggal_berakhir',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_berakhir' => 'date',
            'ulang_setiap' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kebun(): BelongsTo
    {
        return $this->belongsTo(Kebun::class);
    }

    /**
     * Return every occurrence within an inclusive date range.
     *
     * @return Collection<int, Carbon>
     */
    public function occurrencesBetween(CarbonInterface|string $from, CarbonInterface|string $through): Collection
    {
        $rangeStart = $from instanceof CarbonInterface ? Carbon::instance($from) : Carbon::parse($from);
        $rangeEnd = $through instanceof CarbonInterface ? Carbon::instance($through) : Carbon::parse($through);
        $rangeStart->startOfDay();
        $rangeEnd->startOfDay();

        if ($rangeEnd->lt($rangeStart)) {
            return collect();
        }

        $occurrence = $this->firstOccurrenceOnOrAfter($rangeStart);
        $occurrences = collect();
        $scheduleEnd = $this->tanggal_berakhir?->copy()->startOfDay();

        while ($occurrence !== null && $occurrence->lte($rangeEnd)) {
            if ($scheduleEnd !== null && $occurrence->gt($scheduleEnd)) {
                break;
            }

            $occurrences->push($occurrence->copy());

            if (! $this->isRepeating()) {
                break;
            }

            $occurrence = $this->occurrenceAt($this->occurrenceIndex($occurrence) + 1);
        }

        return $occurrences;
    }

    public function nextOccurrenceOnOrAfter(CarbonInterface|string $date): ?Carbon
    {
        $requestedDate = $date instanceof CarbonInterface ? Carbon::instance($date) : Carbon::parse($date);
        $requestedDate->startOfDay();
        $occurrence = $this->firstOccurrenceOnOrAfter($requestedDate);
        $scheduleEnd = $this->tanggal_berakhir?->copy()->startOfDay();

        if ($occurrence !== null && $scheduleEnd !== null && $occurrence->gt($scheduleEnd)) {
            return null;
        }

        return $occurrence;
    }

    private function isRepeating(): bool
    {
        return $this->ulang_setiap !== null && $this->ulang_satuan !== null;
    }

    private function firstOccurrenceOnOrAfter(Carbon $date): ?Carbon
    {
        $start = $this->tanggal_mulai->copy()->startOfDay();

        if (! $this->isRepeating()) {
            return $start->gte($date) ? $start : null;
        }

        if ($start->gte($date)) {
            return $start;
        }

        $interval = max(1, (int) $this->ulang_setiap);

        if ($this->ulang_satuan === self::SATUAN_MINGGU) {
            $days = (int) $start->diffInDays($date);
            $index = intdiv($days, 7 * $interval);
        } else {
            $months = (($date->year - $start->year) * 12) + $date->month - $start->month;
            $index = max(0, intdiv($months, $interval));
        }

        $candidate = $this->occurrenceAt($index);

        if ($candidate !== null && $candidate->lt($date)) {
            $candidate = $this->occurrenceAt($index + 1);
        }

        return $candidate;
    }

    private function occurrenceAt(int $index): ?Carbon
    {
        $start = $this->tanggal_mulai->copy()->startOfDay();

        if (! $this->isRepeating()) {
            return $index === 0 ? $start : null;
        }

        $interval = $index * max(1, (int) $this->ulang_setiap);

        return $this->ulang_satuan === self::SATUAN_MINGGU
            ? $start->addWeeks($interval)
            : $start->addMonthsNoOverflow($interval);
    }

    private function occurrenceIndex(Carbon $date): int
    {
        $start = $this->tanggal_mulai->copy()->startOfDay();
        $interval = max(1, (int) $this->ulang_setiap);

        if ($this->ulang_satuan === self::SATUAN_MINGGU) {
            return intdiv((int) $start->diffInDays($date), 7 * $interval);
        }

        $months = (($date->year - $start->year) * 12) + $date->month - $start->month;

        return intdiv($months, $interval);
    }
}
