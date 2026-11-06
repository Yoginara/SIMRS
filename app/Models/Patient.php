<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'medical_record_number',
        'nik',
        'name',
        'birth_date',
        'gender',
        'phone',
        'address',
        'blood_type',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
