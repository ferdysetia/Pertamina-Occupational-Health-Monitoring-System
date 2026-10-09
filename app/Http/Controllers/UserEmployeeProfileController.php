<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserEmployeeProfileController extends Controller
{
    /**
     * Update or create the employee profile data for the authenticated user.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $employeeId = $user->employee_id;

        $validated = $request->validate([
            'nik' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'nik')->ignore($employeeId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
            'tinggi_badan' => ['nullable', 'numeric', 'min:50', 'max:250'],
            'berat_badan' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'golongan_darah' => ['nullable', 'string', 'in:A,B,AB,O'],
            'rhesus' => ['nullable', 'string', 'in:+,-'],
            'alergi_obat' => ['nullable', 'string', 'max:500'],
            'alergi_makanan' => ['nullable', 'string', 'max:500'],
            'distrik' => ['nullable', 'string', 'max:255'],
            'lokasi_kerja' => ['nullable', 'string', 'max:255'],
            'fungsi' => ['nullable', 'string', 'max:255'],
            'tipe_pekerjaan' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'alamat_jalan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten_kota' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'nama_kontak_darurat' => ['nullable', 'string', 'max:255'],
            'hubungan_kontak_darurat' => ['nullable', 'string', 'max:100'],
            'no_telepon_darurat' => ['nullable', 'string', 'max:50'],
            'bpjs_kesehatan' => ['nullable', 'string', 'max:50'],
            'bpjs_ketenagakerjaan' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }

        unset($validated['photo']);

        if ($user->employee) {
            $employee = $user->employee;
            $employee->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            $employee = Employee::create($validated);
            $user->forceFill(['employee_id' => $employee->id])->save();
        }

        // Keep user name in sync
        if ($user->name !== $validated['name']) {
            $user->forceFill(['name' => $validated['name']])->save();
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'UPDATE_EMPLOYEE_PROFILE',
            'entity_type' => Employee::class,
            'entity_id' => $employee->id,
            'changes' => [
                'nik' => $employee->nik,
                'name' => $employee->name,
                'distrik' => $employee->distrik,
            ],
        ]);

        return back()->with('success', 'Data profil karyawan berhasil diperbarui.');
    }
}
