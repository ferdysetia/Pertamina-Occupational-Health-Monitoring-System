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
            // 1. Golongan Darah & Rhesus
            $table->string('golongan_darah', 5)->nullable()->after('berat_badan');
            $table->string('rhesus', 5)->nullable()->after('golongan_darah');

            // 2. Riwayat Alergi
            $table->text('alergi_obat')->nullable()->after('rhesus');
            $table->text('alergi_makanan')->nullable()->after('alergi_obat');

            // 3. Jaminan Sosial / BPJS
            $table->string('bpjs_kesehatan', 50)->nullable()->after('no_telepon_darurat');
            $table->string('bpjs_ketenagakerjaan', 50)->nullable()->after('bpjs_kesehatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'golongan_darah',
                'rhesus',
                'alergi_obat',
                'alergi_makanan',
                'bpjs_kesehatan',
                'bpjs_ketenagakerjaan',
            ]);
        });
    }
};
