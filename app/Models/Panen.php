<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panen extends Model
{
    protected $fillable = [
        'user_id',
        'kebun_id',
        'tanggal',
        'jumlah_tandan',
        'berat_kg',
        'harga_per_kg',
        'pembeli',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah_tandan' => 'integer',
            'berat_kg' => 'decimal:2',
            'harga_per_kg' => 'decimal:2',
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
