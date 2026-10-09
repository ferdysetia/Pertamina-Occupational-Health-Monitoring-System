<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FollowUp;
use App\Models\HealthAlert;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'health_alert_id' => 'required|exists:health_alerts,id',
            'pic_id' => 'nullable|exists:users,id',
            'notes' => 'required|string',
            'target_date' => 'nullable|date',
            'status' => 'required|string|in:Pending,In Progress,Completed',
        ]);

        $followUp = FollowUp::create($validated);

        // Also update parent HealthAlert status
        $healthAlert = HealthAlert::findOrFail($validated['health_alert_id']);
        if ($validated['status'] === 'Completed') {
            $healthAlert->update(['status' => 'Closed']);
        } elseif ($validated['status'] === 'In Progress') {
            $healthAlert->update(['status' => 'In Progress']);
        }

        // Audit Trail Log
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CREATE_FOLLOW_UP',
            'entity_type' => FollowUp::class,
            'entity_id' => $followUp->id,
            'changes' => $validated,
        ]);

        return redirect()->back()->with('success', 'Catatan tindak lanjut berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $followUp = FollowUp::findOrFail($id);

        $validated = $request->validate([
            'notes' => 'required|string',
            'target_date' => 'nullable|date',
            'status' => 'required|string|in:Pending,In Progress,Completed',
            'pic_id' => 'nullable|exists:users,id',
        ]);

        $followUp->update($validated);

        // Update health alert status
        if ($validated['status'] === 'Completed') {
            $followUp->healthAlert->update(['status' => 'Closed']);
        }

        // Audit Trail Log
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_FOLLOW_UP',
            'entity_type' => FollowUp::class,
            'entity_id' => $followUp->id,
            'changes' => $validated,
        ]);

        return redirect()->back()->with('success', 'Status tindak lanjut berhasil diperbarui.');
    }
}
