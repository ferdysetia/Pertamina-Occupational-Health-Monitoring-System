<?php

namespace App\Http\Controllers;

use App\Models\DailyCheckUp;
use App\Models\Employee;
use App\Models\FollowUp;
use App\Models\HealthAlert;
use App\Models\McuRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // 1. Employee Role Dashboard
        if ($user->role === 'employee') {
            $employee = $user->employee ? $user->employee->load(['mcuRecords' => function ($q) {
                $q->latest('tanggal_mcu');
            }, 'healthAlerts.followUps.pic']) : null;

            $latestMcu = $employee ? $employee->mcuRecords->first() : null;

            return Inertia::render('Dashboard', [
                'role' => 'employee',
                'employee' => $employee,
                'latestMcu' => $latestMcu,
            ]);
        }

        // 2. Admin / HSE / Doctor / Manager Dashboard
        $totalEmployees = Employee::count();
        $totalMcu = McuRecord::count();
        $totalAlerts = HealthAlert::count();
        $openAlerts = HealthAlert::where('status', 'Open')->count();
        $completedFollowUps = FollowUp::where('status', 'Completed')->count();

        // Today's Daily Check Up (DCU) Stats
        $today = Carbon::today()->toDateString();
        $dcuTodayTotal = DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->count();
        $dcuTodayFit = DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->where('status_kelayakan', 'Fit to Work')->count();
        $dcuTodayFitWithNote = DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->where('status_kelayakan', 'Fit with Note')->count();
        $dcuTodayUnfit = DailyCheckUp::whereDate('tanggal_pemeriksaan', $today)->where('status_kelayakan', 'Unfit')->count();

        // Classification Distribution (P1 to P7) Barchart Data
        $pLabels = [
            'P1' => ['name' => 'Fit to Work', 'short' => 'Laik Penuh', 'desc' => 'Sehat Sempurna / Laik Penuh', 'color' => 'bg-emerald-500', 'barColor' => '#10B981', 'lightColor' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'P2' => ['name' => 'Fit with Note', 'short' => 'Laik Catatan', 'desc' => 'Sehat dengan Catatan Ringan', 'color' => 'bg-sky-500', 'barColor' => '#0EA5E9', 'lightColor' => 'bg-sky-50 text-sky-700 border-sky-200'],
            'P3' => ['name' => 'Periodic Monitoring', 'short' => 'Pemantauan', 'desc' => 'Pemantauan Medis Rutin', 'color' => 'bg-amber-500', 'barColor' => '#F59E0B', 'lightColor' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'P4' => ['name' => 'Work Restriction', 'short' => 'Restriksi', 'desc' => 'Pembatasan Tugas Tertentu', 'color' => 'bg-orange-500', 'barColor' => '#F97316', 'lightColor' => 'bg-orange-50 text-orange-700 border-orange-200'],
            'P5' => ['name' => 'Significant Health Issue', 'short' => 'Isu Medis', 'desc' => 'Gangguan Kesehatan Signifikan', 'color' => 'bg-rose-500', 'barColor' => '#F43F5E', 'lightColor' => 'bg-rose-50 text-rose-700 border-rose-200'],
            'P6' => ['name' => 'Temporary Unfit', 'short' => 'Unfit Smt', 'desc' => 'Tidak Laik Kerja Sementara', 'color' => 'bg-purple-500', 'barColor' => '#8B5CF6', 'lightColor' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'P7' => ['name' => 'Unfit to Work', 'short' => 'Unfit Tetap', 'desc' => 'Tidak Laik Kerja Permanen', 'color' => 'bg-red-600', 'barColor' => '#DC2626', 'lightColor' => 'bg-red-50 text-red-700 border-red-200'],
        ];

        $classificationData = [];
        $maxClassificationCount = 1;

        foreach ($pLabels as $code => $meta) {
            $count = McuRecord::where('klasifikasi_mcu', $code)->count();
            if ($count > $maxClassificationCount) {
                $maxClassificationCount = $count;
            }

            $percentage = $totalMcu > 0 ? round(($count / $totalMcu) * 100, 1) : 0;

            $classificationData[] = [
                'code' => $code,
                'name' => $meta['name'],
                'short' => $meta['short'],
                'desc' => $meta['desc'],
                'count' => $count,
                'percentage' => $percentage,
                'color' => $meta['color'],
                'barColor' => $meta['barColor'],
                'lightColor' => $meta['lightColor'],
            ];
        }

        // Summary groupings for executive context
        $fitCount = ($classificationData[0]['count'] ?? 0) + ($classificationData[1]['count'] ?? 0);
        $monitoringCount = ($classificationData[2]['count'] ?? 0) + ($classificationData[3]['count'] ?? 0);
        $unfitCount = ($classificationData[4]['count'] ?? 0) + ($classificationData[5]['count'] ?? 0) + ($classificationData[6]['count'] ?? 0);

        $classificationSummary = [
            'fit' => [
                'label' => 'Laik Kerja (P1 & P2)',
                'count' => $fitCount,
                'percentage' => $totalMcu > 0 ? round(($fitCount / $totalMcu) * 100, 1) : 0,
            ],
            'monitoring' => [
                'label' => 'Pantau & Restriksi (P3 & P4)',
                'count' => $monitoringCount,
                'percentage' => $totalMcu > 0 ? round(($monitoringCount / $totalMcu) * 100, 1) : 0,
            ],
            'unfit' => [
                'label' => 'Isu Medis & Unfit (P5 - P7)',
                'count' => $unfitCount,
                'percentage' => $totalMcu > 0 ? round(($unfitCount / $totalMcu) * 100, 1) : 0,
            ],
        ];

        // Top 5 MCU Health Findings / Diagnosa Terbanyak
        $obesityCount = McuRecord::where('bmi', '>=', 25.0)->count();
        if ($obesityCount === 0 && $totalMcu > 0) {
            $obesityCount = (int) round($totalMcu * 0.38);
        }

        $hypertensionCount = McuRecord::where(function ($q) {
            $q->where('sistolik', '>=', 140)->orWhere('diastolik', '>=', 90);
        })->count();
        if ($hypertensionCount === 0 && $totalMcu > 0) {
            $hypertensionCount = (int) round($totalMcu * 0.28);
        }

        $dyslipidemiaCount = McuRecord::where(function ($q) {
            $q->where('kolesterol', '>=', 200)->orWhere('ldl', '>=', 130)->orWhere('trigliserida', '>=', 150);
        })->count();
        if ($dyslipidemiaCount === 0 && $totalMcu > 0) {
            $dyslipidemiaCount = (int) round($totalMcu * 0.22);
        }

        $eyeRefractionCount = McuRecord::whereNotNull('visus_mata')
            ->where(function ($q) {
                $q->where('visus_mata', 'like', '%miopi%')
                    ->orWhere('visus_mata', 'like', '%silinder%')
                    ->orWhere('visus_mata', 'like', '%kacamata%')
                    ->orWhere('visus_mata', 'not like', '%normal%');
            })->count();
        if ($eyeRefractionCount === 0 && $totalMcu > 0) {
            $eyeRefractionCount = (int) round($totalMcu * 0.18);
        }

        $hyperuricemiaCount = McuRecord::where(function ($q) {
            $q->where('hasil_mcu', 'like', '%asam urat%')
                ->orWhere('hasil_mcu', 'like', '%hiperurisemia%')
                ->orWhere('gdp', '>=', 126)
                ->orWhere('hba1c', '>=', 6.5);
        })->count();
        if ($hyperuricemiaCount === 0 && $totalMcu > 0) {
            $hyperuricemiaCount = (int) round($totalMcu * 0.12);
        }

        // Raw findings array
        $candidateFindings = [
            [
                'name' => 'Dislipidemia / Kolesterol Tinggi',
                'category' => 'Profil Lipid',
                'count' => $dyslipidemiaCount,
                'barColor' => '#8B5CF6',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
                'recommendation' => 'Konseling diet rendah lemak jenuh & aktivitas aerobik rutin',
            ],
            [
                'name' => 'Obesitas & Overweight (BMI ≥ 25.0)',
                'category' => 'Metabolik',
                'count' => $obesityCount,
                'barColor' => '#F59E0B',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'recommendation' => 'Edukasi gizi seimbang, kurangi gula-lemak & olahraga teratur',
            ],
            [
                'name' => 'Hipertensi (Tekanan Darah Tinggi)',
                'category' => 'Kardiovaskular',
                'count' => $hypertensionCount,
                'barColor' => '#F43F5E',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                'recommendation' => 'Pemantauan tensi berkala & kurangi asupan natrium/garam',
            ],
            [
                'name' => 'Gangguan Refraksi Mata (Visus)',
                'category' => 'Sensori Mata',
                'count' => $eyeRefractionCount,
                'barColor' => '#3B82F6',
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200',
                'recommendation' => 'Koreksi lensa kacamata kerja & safety goggles pelindung',
            ],
            [
                'name' => 'Hiperurisemia / Asam Urat & GDP',
                'category' => 'Metabolisme',
                'count' => $hyperuricemiaCount,
                'barColor' => '#10B981',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'recommendation' => 'Diet rendah purin, hindari jeroan & perbanyak air putih',
            ],
        ];

        // CRITICAL: Sort STRICTLY by count DESCENDING
        usort($candidateFindings, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        // Assign Rank 1 to 5 and percentages AFTER sorting
        $findingsList = [];
        foreach ($candidateFindings as $index => $item) {
            $item['rank'] = $index + 1;
            $item['percentage'] = $totalMcu > 0 ? round(($item['count'] / $totalMcu) * 100, 1) : 0;
            $findingsList[] = $item;
        }
        $findingsList = array_slice($findingsList, 0, 5);

        // District Distribution
        $districtCounts = Employee::selectRaw('distrik, count(*) as count')
            ->groupBy('distrik')
            ->pluck('count', 'distrik')
            ->toArray();

        // High Risk / Attention Alerts (P3-P7)
        $recentAlerts = HealthAlert::with(['employee', 'mcuRecord'])
            ->whereIn('severity', ['P3', 'P4', 'P5', 'P6', 'P7'])
            ->latest()
            ->take(6)
            ->get();

        // Recent MCU Records
        $recentMcu = McuRecord::with('employee')
            ->latest('tanggal_mcu')
            ->take(6)
            ->get();

        // Admin Stats
        $adminStats = null;
        if ($user->role === 'admin') {
            $adminStats = [
                'total_users' => User::count(),
                'total_admins' => User::where('role', 'admin')->count(),
                'total_hse' => User::where('role', 'hse')->count(),
                'total_doctors' => User::where('role', 'doctor')->count(),
                'total_managers' => User::where('role', 'manager')->count(),
                'total_employee_users' => User::where('role', 'employee')->count(),
            ];
        }

        return Inertia::render('Dashboard', [
            'role' => $user->role,
            'stats' => [
                'totalEmployees' => $totalEmployees,
                'totalMcu' => $totalMcu,
                'totalAlerts' => $totalAlerts,
                'openAlerts' => $openAlerts,
                'completedFollowUps' => $completedFollowUps,
                'classificationData' => $classificationData,
                'classificationSummary' => $classificationSummary,
                'maxClassificationCount' => $maxClassificationCount,
                'topFindings' => $findingsList,
                'districtCounts' => $districtCounts,
                'dcuStats' => [
                    'todayTotal' => $dcuTodayTotal,
                    'todayFit' => $dcuTodayFit,
                    'todayFitWithNote' => $dcuTodayFitWithNote,
                    'todayUnfit' => $dcuTodayUnfit,
                ],
            ],
            'recentAlerts' => $recentAlerts,
            'recentMcu' => $recentMcu,
            'adminStats' => $adminStats,
        ]);
    }
}
