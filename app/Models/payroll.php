<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payroll extends Model
{


    use HasFactory;
    use HasUuids;

public $incrementing = false;
protected $casts = ['id'=>'string'];
protected $keyType = 'string';

protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    protected $fillable  = [
        'employee_id',
        'payment_date',
        'pay_period',
        'gross_pay',
        'net_pay',
        'commission',
        'bonus',
        'health_insurance',
        'total_tax_amount',
        'other_deductions',
        'payment_method',
        'payment_status'
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, "payroll_employee")->withPivot('salary', 'pay_period', 'earning_description', 'earning_amount', 'deduction_description', 'deduction_amount', 'payee', 'net_salary', 'total_paid');
    }


    public function client()
    {
        return $this->belongsTo(Client::class);

   }
}
