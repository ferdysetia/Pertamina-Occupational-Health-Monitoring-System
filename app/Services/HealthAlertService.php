<?php

namespace App\Services;

use App\Models\HealthAlert;
use App\Models\McuRecord;

class HealthAlertService
{
    /**
     * Evaluate an MCU record and create or update a health alert if needed.
     */
    public function evaluateAndCreateAlert(McuRecord $mcuRecord): ?HealthAlert
    {
        $classification = strtoupper(trim((string) $mcuRecord->klasifikasi_mcu));

        // Evaluate if classification requires attention (P3 to P7)
        if (in_array($classification, ['P3', 'P4', 'P5', 'P6', 'P7'])) {
            $reason = $mcuRecord->hasil_mcu ?: "Klasifikasi MCU {$classification} membutuhkan perhatian khusus.";

            // Categorize alert based on classification or findings
            $category = 'Pemantauan MCU';
            if (str_contains(strtolower($reason), 'jantung') || str_contains(strtolower($reason), 'kardiomegali')) {
                $category = 'Kardiovaskular';
            } elseif (str_contains(strtolower($reason), 'diabetes') || str_contains(strtolower($reason), 'hiperglikemia')) {
                $category = 'Metabolik / Diabetes';
            } elseif (str_contains(strtolower($reason), 'mata') || str_contains(strtolower($reason), 'visus') || str_contains(strtolower($reason), 'miopi')) {
                $category = 'Indera / Mata';
            } elseif (str_contains(strtolower($reason), 'pendengaran') || str_contains(strtolower($reason), 'audiometri')) {
                $category = 'Indera / Telinga';
            } elseif (str_contains(strtolower($reason), 'restriksi') || str_contains(strtolower($reason), 'obstruksi') || str_contains(strtolower($reason), 'tb paru')) {
                $category = 'Respirasi / Paru';
            }

            return HealthAlert::updateOrCreate(
                [
                    'mcu_record_id' => $mcuRecord->id,
                ],
                [
                    'employee_id' => $mcuRecord->employee_id,
                    'severity' => $classification,
                    'category' => $category,
                    'reason' => $reason,
                    'status' => 'Open',
                ]
            );
        }

        return null;
    }
}
