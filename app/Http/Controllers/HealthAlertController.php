<?php

namespace App\Http\Controllers;

use App\Models\HealthAlert;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HealthAlertController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = HealthAlert::with(['employee', 'mcuRecord', 'followUps.pic']);

        if ($user->role === 'employee') {
            $query->where('employee_id', $user->employee_id);
        } else {
            // Filters for Admin / HSE / Doctor
            if ($request->filled('severity')) {
                $query->where('severity', $request->input('severity'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }
        }

        $alerts = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('HealthAlerts/Index', [
            'alerts' => $alerts,
            'filters' => $request->only(['severity', 'status', 'search']),
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $user = $request->user();
        $alert = HealthAlert::with(['employee', 'mcuRecord', 'followUps.pic'])->findOrFail($id);

        if ($user->role === 'employee' && (int) $alert->employee_id !== (int) $user->employee_id) {
            abort(403, 'Anda hanya diperbolehkan melihat Health Alert milik Anda sendiri.');
        }

        $pics = User::whereIn('role', ['hse', 'doctor', 'admin', 'manager'])->select('id', 'name', 'role')->get();

        return Inertia::render('HealthAlerts/Show', [
            'alert' => $alert,
            'pics' => $pics,
        ]);
    }
}
