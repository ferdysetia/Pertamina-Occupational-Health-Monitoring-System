<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\McuRecord;
use App\Services\HealthAlertService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class McuController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Employee role constraint: redirect to own employee profile
        if ($user->role === 'employee') {
            return redirect()->route('employees.show', $user->employee_id);
        }

        $query = McuRecord::with('employee');

        // Search NIK or Name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Klasifikasi filter (P1 to P7)
        if ($request->filled('klasifikasi')) {
            $query->where('klasifikasi_mcu', $request->input('klasifikasi'));
        }

        // District filter
        if ($request->filled('distrik')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('distrik', $request->input('distrik'));
            });
        }

        // Tanggal MCU filter
        if ($request->filled('date')) {
            $query->whereDate('tanggal_mcu', $request->input('date'));
        } elseif ($request->filled('start_date') || $request->filled('end_date')) {
            if ($request->filled('start_date')) {
                $query->whereDate('tanggal_mcu', '>=', $request->input('start_date'));
            }
            if ($request->filled('end_date')) {
                $query->whereDate('tanggal_mcu', '<=', $request->input('end_date'));
            }
        }

        // Fungsi / Departemen filter
        if ($request->filled('fungsi')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('fungsi', $request->input('fungsi'));
            });
        }

        // Jenis Pekerjaan / Tipe Pekerjaan filter
        if ($request->filled('tipe_pekerjaan')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('tipe_pekerjaan', $request->input('tipe_pekerjaan'));
            });
        }

        $mcuRecords = $query->latest('tanggal_mcu')->paginate(15)->withQueryString();

        $distrikOptions = Employee::distinct()->whereNotNull('distrik')->where('distrik', '!=', '')->orderBy('distrik')->pluck('distrik');
        $fungsiOptions = Employee::distinct()->whereNotNull('fungsi')->where('fungsi', '!=', '')->orderBy('fungsi')->pluck('fungsi');
        $tipePekerjaanOptions = Employee::distinct()->whereNotNull('tipe_pekerjaan')->where('tipe_pekerjaan', '!=', '')->orderBy('tipe_pekerjaan')->pluck('tipe_pekerjaan');

        return Inertia::render('MCU/Index', [
            'mcuRecords' => $mcuRecords,
            'filters' => $request->only(['search', 'klasifikasi', 'distrik', 'date', 'start_date', 'end_date', 'fungsi', 'tipe_pekerjaan']),
            'distrikOptions' => $distrikOptions,
            'fungsiOptions' => $fungsiOptions,
            'tipePekerjaanOptions' => $tipePekerjaanOptions,
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $user = $request->user();
        $mcuRecord = McuRecord::with(['employee', 'healthAlerts.followUps.pic'])->findOrFail($id);

        if ($user->role === 'employee' && (int) $mcuRecord->employee_id !== (int) $user->employee_id) {
            abort(403, 'Anda hanya diperbolehkan melihat data MCU milik Anda sendiri.');
        }

        return Inertia::render('MCU/Show', [
            'mcuRecord' => $mcuRecord,
        ]);
    }

    public function create(): Response
    {
        $employees = Employee::select('id', 'nik', 'name', 'distrik', 'fungsi')->orderBy('nik')->get();

        return Inertia::render('MCU/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request, HealthAlertService $healthAlertService)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_mcu' => 'required|date',
            'umur' => 'nullable|integer|min:15|max:100',
            'lingkar_perut' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'bb' => 'nullable|numeric',
            'bmi' => 'nullable|numeric',
            'sistolik' => 'nullable|integer',
            'diastolik' => 'nullable|integer',
            'hba1c' => 'nullable|numeric',
            'gdp' => 'nullable|numeric',
            'kolesterol' => 'nullable|integer',
            'ldl' => 'nullable|numeric',
            'hdl' => 'nullable|integer',
            'trigliserida' => 'nullable|integer',
            'eritrosit' => 'nullable|numeric',
            'leukosit' => 'nullable|numeric',
            'trombosit' => 'nullable|integer',
            'spirometri' => 'nullable|string',
            'foto_thorax' => 'nullable|string',
            'ekg' => 'nullable|string',
            'visus_mata' => 'nullable|string',
            'audiometri' => 'nullable|string',
            'riwayat_alkohol' => 'nullable|string',
            'riwayat_merokok' => 'nullable|string',
            'riwayat_diabetes' => 'nullable|string',
            'skor_kardiovaskular_jakarta' => 'nullable|string',
            'frekuensi_olahraga' => 'nullable|string',
            'hasil_mcu' => 'nullable|string',
            'klasifikasi_mcu' => 'required|string|in:P1,P2,P3,P4,P5,P6,P7',
        ]);

        $mcuRecord = McuRecord::create($validated);

        // Evaluate alert
        $healthAlertService->evaluateAndCreateAlert($mcuRecord);

        // Audit Trail Log
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE_MCU',
            'entity_type' => McuRecord::class,
            'entity_id' => $mcuRecord->id,
            'changes' => $validated,
        ]);

        return redirect()->route('mcu.show', $mcuRecord->id)->with('success', 'Data MCU berhasil ditambahkan.');
    }

    public function edit(int $id): Response
    {
        $mcuRecord = McuRecord::with('employee')->findOrFail($id);
        $employees = Employee::select('id', 'nik', 'name', 'distrik', 'fungsi')->orderBy('nik')->get();

        return Inertia::render('MCU/Edit', [
            'mcuRecord' => $mcuRecord,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, int $id, HealthAlertService $healthAlertService)
    {
        $mcuRecord = McuRecord::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_mcu' => 'required|date',
            'umur' => 'nullable|integer|min:15|max:100',
            'lingkar_perut' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'bb' => 'nullable|numeric',
            'bmi' => 'nullable|numeric',
            'sistolik' => 'nullable|integer',
            'diastolik' => 'nullable|integer',
            'hba1c' => 'nullable|numeric',
            'gdp' => 'nullable|numeric',
            'kolesterol' => 'nullable|integer',
            'ldl' => 'nullable|numeric',
            'hdl' => 'nullable|integer',
            'trigliserida' => 'nullable|integer',
            'eritrosit' => 'nullable|numeric',
            'leukosit' => 'nullable|numeric',
            'trombosit' => 'nullable|integer',
            'spirometri' => 'nullable|string',
            'foto_thorax' => 'nullable|string',
            'ekg' => 'nullable|string',
            'visus_mata' => 'nullable|string',
            'audiometri' => 'nullable|string',
            'riwayat_alkohol' => 'nullable|string',
            'riwayat_merokok' => 'nullable|string',
            'riwayat_diabetes' => 'nullable|string',
            'skor_kardiovaskular_jakarta' => 'nullable|string',
            'frekuensi_olahraga' => 'nullable|string',
            'hasil_mcu' => 'nullable|string',
            'klasifikasi_mcu' => 'required|string|in:P1,P2,P3,P4,P5,P6,P7',
        ]);

        $mcuRecord->update($validated);

        // Evaluate alert
        $healthAlertService->evaluateAndCreateAlert($mcuRecord);

        // Audit Log
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_MCU',
            'entity_type' => McuRecord::class,
            'entity_id' => $mcuRecord->id,
            'changes' => $validated,
        ]);

        return redirect()->route('mcu.show', $mcuRecord->id)->with('success', 'Data MCU berhasil diperbarui.');
    }
}
