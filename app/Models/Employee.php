<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'name',
        'tanggal_lahir',
        'jenis_kelamin',
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'rhesus',
        'alergi_obat',
        'alergi_makanan',
        'distrik',
        'lokasi_kerja',
        'fungsi',
        'tipe_pekerjaan',
        'no_telepon',
        'alamat',
        'alamat_jalan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'nama_kontak_darurat',
        'hubungan_kontak_darurat',
        'no_telepon_darurat',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mcuRecords(): HasMany
    {
        return $this->hasMany(McuRecord::class);
    }

    public function latestMcuRecord(): HasOne
    {
        return $this->hasOne(McuRecord::class)->latestOfMany();
    }

    public function healthAlerts(): HasMany
    {
        return $this->hasMany(HealthAlert::class);
    }

    public function dailyCheckUps(): HasMany
    {
        return $this->hasMany(DailyCheckUp::class);
    }

    public function latestDailyCheckUp(): HasOne
    {
        return $this->hasOne(DailyCheckUp::class)->latestOfMany('tanggal_pemeriksaan');
    }
}
