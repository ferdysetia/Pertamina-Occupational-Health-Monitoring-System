<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCheckUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_pemeriksaan',
        'waktu_pemeriksaan',
        'employee_id',
        'nama_pekerja',
        'nomor_pekerja',
        'tanggal_lahir',
        'usia',
        'jenis_pekerjaan',
        'region',
        'area',
        'lokasi_pekerjaan',
        'detail_lokasi',
        'konsumsi_obat',
        'detail_konsumsi_obat',
        'keluhan_saat_ini',
        'detail_keluhan',
        'riwayat_penyakit',
        'detail_riwayat_penyakit',
        'kesadaran',
        'sistolik',
        'diastolik',
        'nadi_istirahat',
        'irama_nadi',
        'frekuensi_napas',
        'temperatur',
        'saturasi_oksigen',
        'conjungtiva_mata',
        'pupil_mata',
        'keseimbangan',
        'penyalahgunaan_napza',
        'status_kelayakan',
        'catatan_pemeriksa',
        'checked_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pemeriksaan' => 'date',
            'tanggal_lahir' => 'date',
            'usia' => 'integer',
            'sistolik' => 'integer',
            'diastolik' => 'integer',
            'nadi_istirahat' => 'integer',
            'frekuensi_napas' => 'integer',
            'temperatur' => 'decimal:1',
            'saturasi_oksigen' => 'decimal:1',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }
}
