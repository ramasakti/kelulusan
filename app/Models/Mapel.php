<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapel';

    protected $fillable = [
        'nama_mapel',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('orderByNamaMapel', function (Builder $builder) {
            $builder->orderBy('nama_mapel');
        });
    }

    /**
     * Get the nilai for the mapel.
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class, 'mapel_id');
    }
}
