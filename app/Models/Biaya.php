<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Biaya extends Model
{
    public const KATEGORI = [
        'Upah panen',
        'Pupuk',
        'Perawatan',
        'Transportasi',
        'Lainnya',
    ];

    public const MODE_MANUAL = 'manual';

    public const MODE_OTOMATIS = 'otomatis';

    protected $fillable = [
        'user_id',
        'kebun_id',
        'tanggal',
        'kategori',
        'nominal',
        'keterangan',
        'mode_hitung',
        'tarif_per_kg',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_berat_kg',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nominal' => 'decimal:2',
            'tarif_per_kg' => 'decimal:2',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'total_berat_kg' => 'decimal:2',
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
}
