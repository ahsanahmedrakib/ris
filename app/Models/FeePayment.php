<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'fee_payments';

    protected $fillable = [
        'invoice_id',
        'student_id',
        'amount',
        'payment_method',
        'transaction_id',
        'paid_by',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'invoice_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
