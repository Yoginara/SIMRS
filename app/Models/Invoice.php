<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'registration_id',
        'consultation_fee',
        'medicine_fee',
        'total_amount',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'consultation_fee' => 'decimal:2',
            'medicine_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
