<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DailyCheckUp;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DailyCheckUpController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = DailyCheckUp::with(['employee', 'examiner']);

        // Role restriction: employee only views own records
        if ($user->role === 'employee') {
            $query->where('employee_id', $user->employee_id);
        } else {
            // Search filter
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pekerja', 'like', "%{$search}%")
                        ->orWhere('nomor_pekerja', 'like', "%{$search}%");
                });
            }

            // Date filter
            if ($request->filled('date')) {
                $query->whereDate('tanggal_pemeriksaan', $request->input('date'));
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status_kelayakan', $request->input('status'));
            }

            // Area / Region filter
            if ($request->filled('area')) {
                $query->where('area', $request->input('area'));
            }
        }

        $dailyCheckUps = $query->latest('tanggal_pemeriksaan')
            ->latest('waktu_pemeriksaan')
            ->paginate(15)
            ->withQueryString();

        // Summary Metrics for Today
        $today = now()->toDateString();
        $statsToday = [
            'total' => DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->count(),
            'fit' => DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->where('status_kelayakan', 'Fit to Work')->count(),
            'fit_with_note' => DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->where('status_kelayakan', 'Fit with Note')->count(),
            'unfit' => DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->where('status_kelayakan', 'Unfit')->count(),
        ];

        $areaOptions = DailyCheckUp::distinct()->whereNotNull('area')->pluck('area');

        return Inertia::render('DailyCheckUp/Index', [
            'dailyCheckUps' => $dailyCheckUps,
            'statsToday' => $statsToday,
            'filters' => $request->only(['search', 'date', 'status', 'area']),
            'areaOptions' => $areaOptions,
        ]);
    }

    public function create(): Response
    {
        $employees = Employee::with('latestMcuRecord')
            ->select('id', 'nik', 'name', 'distrik', 'fungsi', 'tipe_pekerjaan')
            ->orderBy('nik')
            ->get();

        return Inertia::render('DailyCheckUp/Create', [
            'employees' => $employees,
            'defaultDate' => now()->toDateString(),
            'defaultTime' => now()->format('H:i'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'waktu_pemeriksaan' => 'required',
            'employee_id' => 'required|exists:employees,id',
            'nama_pekerja' => 'required|string|max:255',
            'nomor_pekerja' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer|min:15|max:100',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'lokasi_pekerjaan' => 'nullable|string|max:255',
            'detail_lokasi' => 'nullable|string|max:255',

            // Tanya Jawab
            'konsumsi_obat' => 'required|string|in:Ya,Tidak',
            'detail_konsumsi_obat' => 'nullable|string',
            'keluhan_saat_ini' => 'required|string|in:Ya,Tidak',
            'detail_keluhan' => 'nullable|string',
            'riwayat_penyakit' => 'required|string|in:Ya,Tidak',
            'detail_riwayat_penyakit' => 'nullable|string',

            // Pemeriksaan Fisik
            'kesadaran' => 'required|string|in:Compos Mentis,Hilang Sadar,Somnolen,Apatis',
            'sistolik' => 'nullable|integer|min:50|max:300',
            'diastolik' => 'nullable|integer|min:30|max:200',
            'nadi_istirahat' => 'nullable|integer|min:30|max:250',
            'irama_nadi' => 'required|string|in:Teratur,Tidak Teratur',
            'frekuensi_napas' => 'nullable|integer|min:5|max:60',
            'temperatur' => 'nullable|numeric|min:30|max:45',
            'saturasi_oksigen' => 'nullable|numeric|min:50|max:100',
            'conjungtiva_mata' => 'required|string|in:Tidak Pucat,Pucat',
            'pupil_mata' => 'required|string|in:Normal,Tidak Normal',
            'keseimbangan' => 'required|string|in:Negatif,Positif',
            'penyalahgunaan_napza' => 'required|string|in:Negatif,Positif',

            // Kesimpulan
            'status_kelayakan' => 'required|string|in:Fit to Work,Fit with Note,Unfit',
            'catatan_pemeriksa' => 'nullable|string',
        ]);

        $validated['checked_by_user_id'] = $request->user()->id;

        $dailyCheckUp = DailyCheckUp::create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE_DAILY_CHECK_UP',
            'entity_type' => DailyCheckUp::class,
            'entity_id' => $dailyCheckUp->id,
            'changes' => [
                'nama_pekerja' => $dailyCheckUp->nama_pekerja,
                'status_kelayakan' => $dailyCheckUp->status_kelayakan,
                'tanggal_pemeriksaan' => $dailyCheckUp->tanggal_pemeriksaan,
            ],
        ]);

        return redirect()->route('daily-check-ups.show', $dailyCheckUp->id)
            ->with('success', 'Data Daily Check Up berhasil disimpan.');
    }

    public function show(Request $request, int $id): Response
    {
        $user = $request->user();
        $dailyCheckUp = DailyCheckUp::with(['employee.latestMcuRecord', 'examiner'])->findOrFail($id);

        if ($user->role === 'employee' && (int) $dailyCheckUp->employee_id !== (int) $user->employee_id) {
            abort(403, 'Anda hanya diperbolehkan melihat data Daily Check Up milik Anda sendiri.');
        }

        return Inertia::render('DailyCheckUp/Show', [
            'dailyCheckUp' => $dailyCheckUp,
        ]);
    }

    public function edit(int $id): Response
    {
        $dailyCheckUp = DailyCheckUp::with('employee')->findOrFail($id);
        $employees = Employee::select('id', 'nik', 'name', 'distrik', 'fungsi', 'tipe_pekerjaan')->orderBy('nik')->get();

        return Inertia::render('DailyCheckUp/Edit', [
            'dailyCheckUp' => $dailyCheckUp,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $dailyCheckUp = DailyCheckUp::findOrFail($id);

        $validated = $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'waktu_pemeriksaan' => 'required',
            'employee_id' => 'required|exists:employees,id',
            'nama_pekerja' => 'required|string|max:255',
            'nomor_pekerja' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer|min:15|max:100',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'lokasi_pekerjaan' => 'nullable|string|max:255',
            'detail_lokasi' => 'nullable|string|max:255',

            // Tanya Jawab
            'konsumsi_obat' => 'required|string|in:Ya,Tidak',
            'detail_konsumsi_obat' => 'nullable|string',
            'keluhan_saat_ini' => 'required|string|in:Ya,Tidak',
            'detail_keluhan' => 'nullable|string',
            'riwayat_penyakit' => 'required|string|in:Ya,Tidak',
            'detail_riwayat_penyakit' => 'nullable|string',

            // Pemeriksaan Fisik
            'kesadaran' => 'required|string|in:Compos Mentis,Hilang Sadar,Somnolen,Apatis',
            'sistolik' => 'nullable|integer|min:50|max:300',
            'diastolik' => 'nullable|integer|min:30|max:200',
            'nadi_istirahat' => 'nullable|integer|min:30|max:250',
            'irama_nadi' => 'required|string|in:Teratur,Tidak Teratur',
            'frekuensi_napas' => 'nullable|integer|min:5|max:60',
            'temperatur' => 'nullable|numeric|min:30|max:45',
            'saturasi_oksigen' => 'nullable|numeric|min:50|max:100',
            'conjungtiva_mata' => 'required|string|in:Tidak Pucat,Pucat',
            'pupil_mata' => 'required|string|in:Normal,Tidak Normal',
            'keseimbangan' => 'required|string|in:Negatif,Positif',
            'penyalahgunaan_napza' => 'required|string|in:Negatif,Positif',

            // Kesimpulan
            'status_kelayakan' => 'required|string|in:Fit to Work,Fit with Note,Unfit',
            'catatan_pemeriksa' => 'nullable|string',
        ]);

        $dailyCheckUp->update($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_DAILY_CHECK_UP',
            'entity_type' => DailyCheckUp::class,
            'entity_id' => $dailyCheckUp->id,
            'changes' => [
                'status_kelayakan' => $dailyCheckUp->status_kelayakan,
            ],
        ]);

        return redirect()->route('daily-check-ups.show', $dailyCheckUp->id)
            ->with('success', 'Data Daily Check Up berhasil diperbarui.');
    }
}
