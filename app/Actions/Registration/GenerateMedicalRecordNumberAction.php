<?php

namespace App\Actions\Registration;

use App\Models\Patient;

class GenerateMedicalRecordNumberAction
{
    /**
     * Generate a unique sequential medical record number: RM-YYYYMM-XXXX.
     */
    public function execute(): string
    {
        $prefix = 'RM-' . date('Ym') . '-';

        $latestPatient = Patient::where('medical_record_number', 'like', $prefix . '%')
            ->orderByDesc('medical_record_number')
            ->first();

        if (! $latestPatient) {
            $nextSequence = 1;
        } else {
            $lastSequence = (int) substr($latestPatient->medical_record_number, -4);
            $nextSequence = $lastSequence + 1;
        }

        return $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
