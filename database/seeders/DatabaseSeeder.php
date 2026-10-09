<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run Health Data Import Command
        Artisan::call('data:import-health');

        $hashedPassword = Hash::make('password');

        // 2. Create Default Admin User
        User::firstOrCreate(
            ['email' => 'admin@pertaminagas.id'],
            [
                'name' => 'Administrator HSE',
                'password' => $hashedPassword,
                'role' => 'admin',
            ]
        );

        // 3. Create Default HSE Officer User
        User::firstOrCreate(
            ['email' => 'hse@pertaminagas.id'],
            [
                'name' => 'Tim HSE Officer',
                'password' => $hashedPassword,
                'role' => 'hse',
            ]
        );

        // 4. Create Default Doctor User
        User::firstOrCreate(
            ['email' => 'dokter@pertaminagas.id'],
            [
                'name' => 'dr. Hendra (Dokter Perusahaan)',
                'password' => $hashedPassword,
                'role' => 'doctor',
            ]
        );

        // 5. Create Default Manager HSE User
        User::firstOrCreate(
            ['email' => 'manager@pertaminagas.id'],
            [
                'name' => 'Budi Santoso (Manager HSE)',
                'password' => $hashedPassword,
                'role' => 'manager',
            ]
        );

        // 6. Create Sample Employee User (karyawan@pertaminagas.id)
        $firstEmployee = Employee::first();
        if ($firstEmployee) {
            User::firstOrCreate(
                ['email' => 'karyawan@pertaminagas.id'],
                [
                    'name' => "Karyawan {$firstEmployee->nik}",
                    'password' => $hashedPassword,
                    'role' => 'employee',
                    'employee_id' => $firstEmployee->id,
                ]
            );
        }

        // 6. Create Employee Users for each Employee
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $email = strtolower($employee->nik).'@pertaminagas.id';

            $empUser = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Karyawan {$employee->nik}",
                    'password' => $hashedPassword,
                    'role' => 'employee',
                    'employee_id' => $employee->id,
                ]
            );

            if (! $employee->user_id) {
                $employee->update(['user_id' => $empUser->id]);
            }
        }
    }
}
