<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'employee_id' => $user->employee_id,
                    'profile_photo_url' => $user->profile_photo_url,
                    'employee' => $user->employee ? [
                        'id' => $user->employee->id,
                        'nik' => $user->employee->nik,
                        'name' => $user->employee->name,
                        'tanggal_lahir' => $user->employee->tanggal_lahir,
                        'jenis_kelamin' => $user->employee->jenis_kelamin,
                        'tinggi_badan' => $user->employee->tinggi_badan,
                        'berat_badan' => $user->employee->berat_badan,
                        'golongan_darah' => $user->employee->golongan_darah,
                        'rhesus' => $user->employee->rhesus,
                        'alergi_obat' => $user->employee->alergi_obat,
                        'alergi_makanan' => $user->employee->alergi_makanan,
                        'distrik' => $user->employee->distrik,
                        'lokasi_kerja' => $user->employee->lokasi_kerja,
                        'fungsi' => $user->employee->fungsi,
                        'tipe_pekerjaan' => $user->employee->tipe_pekerjaan,
                        'no_telepon' => $user->employee->no_telepon,
                        'alamat' => $user->employee->alamat,
                        'alamat_jalan' => $user->employee->alamat_jalan,
                        'kecamatan' => $user->employee->kecamatan,
                        'kabupaten_kota' => $user->employee->kabupaten_kota,
                        'provinsi' => $user->employee->provinsi,
                        'kode_pos' => $user->employee->kode_pos,
                        'nama_kontak_darurat' => $user->employee->nama_kontak_darurat,
                        'hubungan_kontak_darurat' => $user->employee->hubungan_kontak_darurat,
                        'no_telepon_darurat' => $user->employee->no_telepon_darurat,
                        'bpjs_kesehatan' => $user->employee->bpjs_kesehatan,
                        'bpjs_ketenagakerjaan' => $user->employee->bpjs_ketenagakerjaan,
                    ] : null,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
