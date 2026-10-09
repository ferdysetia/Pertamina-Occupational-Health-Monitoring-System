<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('alamat_jalan')->nullable()->after('alamat');
            $table->string('kecamatan')->nullable()->after('alamat_jalan');
            $table->string('kabupaten_kota')->nullable()->after('kecamatan');
            $table->string('provinsi')->nullable()->after('kabupaten_kota');
            $table->string('kode_pos', 10)->nullable()->after('provinsi');

            // Kontak Darurat
            $table->string('nama_kontak_darurat')->nullable()->after('kode_pos');
            $table->string('hubungan_kontak_darurat')->nullable()->after('nama_kontak_darurat');
            $table->string('no_telepon_darurat')->nullable()->after('hubungan_kontak_darurat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_jalan',
                'kecamatan',
                'kabupaten_kota',
                'provinsi',
                'kode_pos',
                'nama_kontak_darurat',
                'hubungan_kontak_darurat',
                'no_telepon_darurat',
            ]);
        });
    }
};
