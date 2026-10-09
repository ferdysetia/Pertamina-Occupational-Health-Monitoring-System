<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Employee can only access their own profile detail page
        if ($user->role === 'employee') {
            if (! $user->employee_id) {
                abort(404, 'Data karyawan tidak ditemukan.');
            }

            return redirect()->route('employees.show', $user->employee_id);
        }

        $query = Employee::with(['latestMcuRecord']);

        // Search filter (NIK or Name)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // District filter
        if ($request->filled('distrik')) {
            $query->where('distrik', $request->input('distrik'));
        }

        // Department / Fungsi filter
        if ($request->filled('fungsi')) {
            $query->where('fungsi', $request->input('fungsi'));
        }

        // Work type filter
        if ($request->filled('tipe_pekerjaan')) {
            $query->where('tipe_pekerjaan', $request->input('tipe_pekerjaan'));
        }

        $employees = $query->orderBy('nik')->paginate(15)->withQueryString();

        // Get unique options for filter dropdowns
        $distrikOptions = Employee::distinct()->whereNotNull('distrik')->pluck('distrik');
        $fungsiOptions = Employee::distinct()->whereNotNull('fungsi')->pluck('fungsi');

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
            'filters' => $request->only(['search', 'distrik', 'fungsi', 'tipe_pekerjaan']),
            'distrikOptions' => $distrikOptions,
            'fungsiOptions' => $fungsiOptions,
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $user = $request->user();

        // Employee role constraint: can only see self
        if ($user->role === 'employee' && (int) $user->employee_id !== (int) $id) {
            abort(403, 'Anda hanya diperbolehkan melihat data kesehatan milik Anda sendiri.');
        }

        $employee = Employee::with([
            'mcuRecords' => function ($q) {
                $q->latest('tanggal_mcu');
            },
            'dailyCheckUps' => function ($q) {
                $q->latest('tanggal_pemeriksaan')->latest('waktu_pemeriksaan');
            },
            'healthAlerts.followUps.pic',
        ])->findOrFail($id);

        return Inertia::render('Employees/Show', [
            'employee' => $employee,
        ]);
    }
}
