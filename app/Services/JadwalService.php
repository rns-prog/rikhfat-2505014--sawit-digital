<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class JadwalService
{
    /**
     * @return Collection<int, array{id: int, date: string, type: string, title: string, kebun: ?string}>
     */
    public function eventsForRange(User $user, CarbonInterface|string $from, CarbonInterface|string $through): Collection
    {
        return $user->jadwals()
            ->with('kebun:id,nama')
            ->get()
            ->flatMap(fn (Jadwal $jadwal): Collection => $jadwal
                ->occurrencesBetween($from, $through)
                ->map(fn (Carbon $date): array => $this->event($jadwal, $date)))
            ->sortBy(fn (array $event): string => $event['date'].'|'.$event['type'].'|'.$event['title'])
            ->values();
    }

    /**
     * @return Collection<int, array{id: int, date: string, type: string, title: string, kebun: ?string, days_until: int, countdown: string}>
     */
    public function upcoming(User $user, CarbonInterface|string $from, int $limit = 5): Collection
    {
        $date = $from instanceof CarbonInterface ? Carbon::instance($from)->startOfDay() : Carbon::parse($from)->startOfDay();
        $schedules = $user->jadwals()->with('kebun:id,nama')->get();
        $upcoming = collect();

        while ($upcoming->count() < $limit) {
            $nextEvents = $schedules
                ->map(function (Jadwal $jadwal) use ($date): ?array {
                    $occurrence = $jadwal->nextOccurrenceOnOrAfter($date);

                    return $occurrence ? $this->event($jadwal, $occurrence) : null;
                })
                ->filter()
                ->sortBy(fn (array $event): string => $event['date'].'|'.$event['type'].'|'.$event['title'])
                ->values();

            if ($nextEvents->isEmpty()) {
                break;
            }

            $occurrenceDate = Carbon::parse($nextEvents->first()['date']);

            foreach ($nextEvents->take($limit - $upcoming->count()) as $event) {
                $daysUntil = (int) $date->diffInDays($occurrenceDate);
                $event['days_until'] = $daysUntil;
                $event['countdown'] = match ($daysUntil) {
                    0 => 'Hari ini',
                    1 => 'Besok',
                    default => 'H-'.$daysUntil.' hari',
                };
                $upcoming->push($event);
            }

            $date = $occurrenceDate->addDay();
        }

        return $upcoming;
    }

    /**
     * @return array{id: int, date: string, type: string, title: string, kebun: ?string}
     */
    private function event(Jadwal $jadwal, Carbon $date): array
    {
        return [
            'id' => $jadwal->id,
            'date' => $date->toDateString(),
            'type' => $jadwal->jenis,
            'title' => $jadwal->judul,
            'kebun' => $jadwal->kebun?->nama,
        ];
    }
}
