<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'doctor_id',
        'chief_complaint',
        'vital_signs',
        'physical_examination',
        'icd10_code',
        'diagnosis_name',
        'doctor_notes',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'vital_signs' => 'array',
            'finalized_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function isFinalized(): bool
    {
        return $this->finalized_at !== null;
    }
}
