<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    use HasFactory;

    protected $table = 'fee_invoices';

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'amount',
        'paid_amount',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class, 'invoice_id');
    }

    protected function amount(): Attribute
    {
        return Attribute::make(get: fn ($value) => $value ?? 0);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->attributes['amount'] ?? 0);
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) ($this->attributes['amount'] ?? 0) - (float) ($this->attributes['paid_amount'] ?? 0));
    }

    public function getInvoiceNoAttribute(): string
    {
        return 'RIS-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getFeeTypeAttribute(): ?string
    {
        return $this->feeStructure?->name;
    }

    public function getDateAttribute(): ?string
    {
        return $this->created_at?->format('Y-m-d');
    }

    public function payments()
    {
        return $this->feePayments();
    }
}
