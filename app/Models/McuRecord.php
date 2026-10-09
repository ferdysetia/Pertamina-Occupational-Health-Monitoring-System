<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class McuRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tanggal_mcu',
        'umur',
        'lingkar_perut',
        'tb',
        'bb',
        'bmi',
        'sistolik',
        'diastolik',
        'hba1c',
        'gdp',
        'kolesterol',
        'ldl',
        'hdl',
        'trigliserida',
        'eritrosit',
        'leukosit',
        'trombosit',
        'spirometri',
        'foto_thorax',
        'ekg',
        'visus_mata',
        'audiometri',
        'riwayat_alkohol',
        'riwayat_merokok',
        'riwayat_diabetes',
        'skor_kardiovaskular_jakarta',
        'frekuensi_olahraga',
        'hasil_mcu',
        'klasifikasi_mcu',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mcu' => 'date',
            'umur' => 'integer',
            'lingkar_perut' => 'decimal:2',
            'tb' => 'decimal:2',
            'bb' => 'decimal:2',
            'bmi' => 'decimal:2',
            'sistolik' => 'integer',
            'diastolik' => 'integer',
            'hba1c' => 'decimal:2',
            'gdp' => 'decimal:2',
            'kolesterol' => 'integer',
            'ldl' => 'decimal:2',
            'hdl' => 'integer',
            'trigliserida' => 'integer',
            'eritrosit' => 'decimal:2',
            'leukosit' => 'decimal:2',
            'trombosit' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function healthAlerts(): HasMany
    {
        return $this->hasMany(HealthAlert::class);
    }
}
