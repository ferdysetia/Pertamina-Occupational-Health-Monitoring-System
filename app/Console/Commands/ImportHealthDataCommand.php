<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\McuRecord;
use App\Services\HealthAlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportHealthDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:import-health {--file= : Path to the source SQL file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee health & MCU data from source SQL file into normalized tables without modifying original data values.';

    /**
     * Execute the console command.
     */
    public function handle(HealthAlertService $healthAlertService): int
    {
        $filePath = $this->option('file') ?: base_path('Data kesehatan para karyawan.sql');

        if (! File::exists($filePath)) {
            $this->error("File SQL sumber tidak ditemukan di: {$filePath}");

            return Command::FAILURE;
        }

        $this->info("Membaca file SQL sumber: {$filePath}...");
        $sqlContent = File::get($filePath);

        // Match all tuples starting with ('PTM-
        preg_match_all("/\('PTM-[^)]*(?:\)[^,)]*)*?\)(?=,|\s*;|\r?\n)/s", $sqlContent, $matches);

        // Fallback: match line by line if preg_match_all didn't catch multi-line
        $tuples = $matches[0] ?? [];

        if (empty($tuples)) {
            // Alternative parsing: split by line and pick lines starting with ('PTM-
            $lines = explode("\n", $sqlContent);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (str_starts_with($trimmed, "('PTM-")) {
                    $tuples[] = rtrim(rtrim($trimmed, ';'), ',');
                }
            }
        }

        $totalRows = count($tuples);
        $this->info("Ditemukan {$totalRows} baris data MCU dalam SQL.");

        if ($totalRows === 0) {
            $this->error('Gagal mengidentifikasi baris data MCU dari file SQL.');

            return Command::FAILURE;
        }

        DB::beginTransaction();

        try {
            $importedEmployees = 0;
            $importedMcuRecords = 0;
            $alertsCreated = 0;

            foreach ($tuples as $tupleSql) {
                $rowContent = trim($tupleSql);
                if (str_starts_with($rowContent, '(')) {
                    $rowContent = substr($rowContent, 1);
                }
                if (str_ends_with($rowContent, ')')) {
                    $rowContent = substr($rowContent, 0, -1);
                }

                // Parse CSV fields
                $fields = str_getcsv($rowContent, ',', "'");

                if (count($fields) < 32) {
                    $this->warn('Baris data tidak lengkap ('.count($fields)." kolom): {$tupleSql}");

                    continue;
                }

                $nik = trim($fields[0]); // no e.g. PTM-001
                $distrik = trim($fields[1]);
                $fungsi = trim($fields[2]);
                $tipe_pekerjaan = trim($fields[3]);
                $umur = is_numeric($fields[4]) ? (int) $fields[4] : null;
                $lingkar_perut = is_numeric($fields[5]) ? (float) $fields[5] : null;
                $tb = is_numeric($fields[6]) ? (float) $fields[6] : null;
                $bb = is_numeric($fields[7]) ? (float) $fields[7] : null;
                $bmi = is_numeric($fields[8]) ? (float) $fields[8] : null;
                $sistolik = is_numeric($fields[9]) ? (int) $fields[9] : null;
                $diastolik = is_numeric($fields[10]) ? (int) $fields[10] : null;
                $hba1c = is_numeric($fields[11]) ? (float) $fields[11] : null;
                $gdp = is_numeric($fields[12]) ? (float) $fields[12] : null;
                $kolesterol = is_numeric($fields[13]) ? (int) $fields[13] : null;
                $ldl = is_numeric($fields[14]) ? (float) $fields[14] : null;
                $hdl = is_numeric($fields[15]) ? (int) $fields[15] : null;
                $trigliserida = is_numeric($fields[16]) ? (int) $fields[16] : null;
                $eritrosit = is_numeric($fields[17]) ? (float) $fields[17] : null;
                $leukosit = is_numeric($fields[18]) ? (float) $fields[18] : null;
                $trombosit = is_numeric($fields[19]) ? (int) $fields[19] : null;
                $spirometri = trim($fields[20]);
                $foto_thorax = trim($fields[21]);
                $ekg = trim($fields[22]);
                $visus_mata = trim($fields[23]);
                $audiometri = trim($fields[24]);
                $riwayat_alkohol = trim($fields[25]);
                $riwayat_merokok = trim($fields[26]);
                $riwayat_diabetes = trim($fields[27]);
                $skor_kardiovaskular_jakarta = trim($fields[28]);
                $frekuensi_olahraga = trim($fields[29]);
                $hasil_mcu = trim($fields[30]);
                $klasifikasi_mcu = trim($fields[31]);

                // Create or Update Employee
                $employee = Employee::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'name' => "Karyawan {$nik}",
                        'distrik' => $distrik,
                        'fungsi' => $fungsi,
                        'tipe_pekerjaan' => $tipe_pekerjaan,
                    ]
                );

                if ($employee->wasRecentlyCreated) {
                    $importedEmployees++;
                }

                // Create or Update MCU Record
                $mcuRecord = McuRecord::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'tanggal_mcu' => '2026-10-01',
                    ],
                    [
                        'umur' => $umur,
                        'lingkar_perut' => $lingkar_perut,
                        'tb' => $tb,
                        'bb' => $bb,
                        'bmi' => $bmi,
                        'sistolik' => $sistolik,
                        'diastolik' => $diastolik,
                        'hba1c' => $hba1c,
                        'gdp' => $gdp,
                        'kolesterol' => $kolesterol,
                        'ldl' => $ldl,
                        'hdl' => $hdl,
                        'trigliserida' => $trigliserida,
                        'eritrosit' => $eritrosit,
                        'leukosit' => $leukosit,
                        'trombosit' => $trombosit,
                        'spirometri' => $spirometri,
                        'foto_thorax' => $foto_thorax,
                        'ekg' => $ekg,
                        'visus_mata' => $visus_mata,
                        'audiometri' => $audiometri,
                        'riwayat_alkohol' => $riwayat_alkohol,
                        'riwayat_merokok' => $riwayat_merokok,
                        'riwayat_diabetes' => $riwayat_diabetes,
                        'skor_kardiovaskular_jakarta' => $skor_kardiovaskular_jakarta,
                        'frekuensi_olahraga' => $frekuensi_olahraga,
                        'hasil_mcu' => $hasil_mcu,
                        'klasifikasi_mcu' => $klasifikasi_mcu,
                    ]
                );

                $importedMcuRecords++;

                // Evaluate Health Alert
                $alert = $healthAlertService->evaluateAndCreateAlert($mcuRecord);
                if ($alert) {
                    $alertsCreated++;
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('==========================================');
            $this->info('   LAPORAN PROSES IMPORT DATA KESEHATAN   ');
            $this->info('==========================================');
            $this->info("Jumlah Baris Sumber SQL: {$totalRows}");
            $this->info('Total Karyawan Diimpor  : '.Employee::count());
            $this->info('Total MCU Records       : '.McuRecord::count());
            $this->info('Total Health Alerts     : '.$alertsCreated);
            $this->info('==========================================');

            if (Employee::count() === $totalRows && McuRecord::count() === $totalRows) {
                $this->info('SUCCESS: 100% Data sumber berhasil terimpor tanpa data yang hilang!');
            } else {
                $this->warn('WARNING: Jumlah record diimpor ('.McuRecord::count().") berbeda dengan sumber SQL ({$totalRows}).");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan saat mengimpor data: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
