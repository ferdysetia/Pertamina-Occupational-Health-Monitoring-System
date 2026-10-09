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
        Schema::create('mcu_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('tanggal_mcu')->default(now()->toDateString());
            $table->integer('umur')->nullable();
            $table->decimal('lingkar_perut', 6, 2)->nullable();
            $table->decimal('tb', 6, 2)->nullable();
            $table->decimal('bb', 6, 2)->nullable();
            $table->decimal('bmi', 6, 2)->nullable();
            $table->integer('sistolik')->nullable();
            $table->integer('diastolik')->nullable();
            $table->decimal('hba1c', 6, 2)->nullable();
            $table->decimal('gdp', 6, 2)->nullable();
            $table->integer('kolesterol')->nullable();
            $table->decimal('ldl', 6, 2)->nullable();
            $table->integer('hdl')->nullable();
            $table->integer('trigliserida')->nullable();
            $table->decimal('eritrosit', 6, 2)->nullable();
            $table->decimal('leukosit', 6, 2)->nullable();
            $table->integer('trombosit')->nullable();
            $table->string('spirometri')->nullable();
            $table->string('foto_thorax')->nullable();
            $table->string('ekg')->nullable();
            $table->string('visus_mata')->nullable();
            $table->string('audiometri')->nullable();
            $table->string('riwayat_alkohol')->nullable();
            $table->string('riwayat_merokok')->nullable();
            $table->string('riwayat_diabetes')->nullable();
            $table->string('skor_kardiovaskular_jakarta')->nullable();
            $table->string('frekuensi_olahraga')->nullable();
            $table->text('hasil_mcu')->nullable();
            $table->string('klasifikasi_mcu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcu_records');
    }
};
