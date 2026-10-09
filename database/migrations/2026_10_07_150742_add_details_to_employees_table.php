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
            $table->date('tanggal_lahir')->nullable()->after('name');
            $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->after('jenis_kelamin');
            $table->decimal('berat_badan', 5, 2)->nullable()->after('tinggi_badan');
            $table->string('lokasi_kerja')->nullable()->after('distrik');
            $table->string('no_telepon')->nullable()->after('tipe_pekerjaan');
            $table->text('alamat')->nullable()->after('no_telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_lahir',
                'jenis_kelamin',
                'tinggi_badan',
                'berat_badan',
                'lokasi_kerja',
                'no_telepon',
                'alamat',
            ]);
        });
    }
};
