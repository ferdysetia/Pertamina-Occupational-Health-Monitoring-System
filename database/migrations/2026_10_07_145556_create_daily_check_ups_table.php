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
        Schema::create('daily_check_ups', function (Blueprint $table) {
            $table->id();

            // 1. Tanggal & Waktu Pemeriksaan
            $table->date('tanggal_pemeriksaan');
            $table->time('waktu_pemeriksaan');

            // 2. Identitas & Lokasi Pekerja
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('nama_pekerja');
            $table->string('nomor_pekerja'); // NIK
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('jenis_pekerjaan')->nullable();
            $table->string('region')->nullable();
            $table->string('area')->nullable();
            $table->string('lokasi_pekerjaan')->nullable();
            $table->string('detail_lokasi')->nullable();

            // 3. Tanya Jawab (Anamnesis Singkat)
            $table->string('konsumsi_obat')->default('Tidak'); // Ya / Tidak
            $table->text('detail_konsumsi_obat')->nullable();
            $table->string('keluhan_saat_ini')->default('Tidak'); // Ya / Tidak
            $table->text('detail_keluhan')->nullable();
            $table->string('riwayat_penyakit')->default('Tidak'); // Ya / Tidak
            $table->text('detail_riwayat_penyakit')->nullable();

            // 4. Pemeriksaan Fisik
            $table->string('kesadaran')->default('Compos Mentis'); // Compos Mentis / Hilang Sadar
            $table->integer('sistolik')->nullable(); // mmHg
            $table->integer('diastolik')->nullable(); // mmHg
            $table->integer('nadi_istirahat')->nullable(); // bpm
            $table->string('irama_nadi')->default('Teratur'); // Teratur / Tidak Teratur
            $table->integer('frekuensi_napas')->nullable(); // x/menit
            $table->decimal('temperatur', 4, 1)->nullable(); // °C
            $table->decimal('saturasi_oksigen', 4, 1)->nullable(); // %
            $table->string('conjungtiva_mata')->default('Tidak Pucat'); // Tidak Pucat / Pucat
            $table->string('pupil_mata')->default('Normal'); // Normal / Tidak Normal
            $table->string('keseimbangan')->default('Negatif'); // Negatif / Positif
            $table->string('penyalahgunaan_napza')->default('Negatif'); // Negatif / Positif

            // 5. Kesimpulan & Penilai
            $table->string('status_kelayakan')->default('Fit to Work'); // Fit to Work, Fit with Note, Unfit
            $table->text('catatan_pemeriksa')->nullable();
            $table->foreignId('checked_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_check_ups');
    }
};
