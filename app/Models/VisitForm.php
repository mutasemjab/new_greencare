<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitForm extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'submitted_by', 'code', 'discount_value'];

    protected $casts = [
        'discount_value' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function (VisitForm $visitForm) {
            if (empty($visitForm->code)) {
                $visitForm->code = self::generateCode();
            }
        });
    }

    private static function generateCode(): string
    {
        do {
            $code = 'VF-' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Whether the given user may use this visit form's code — the patient
     * it was filled for, or the super_nurse who filled it (they're
     * typically the one entering the code on the patient's behalf during
     * the visit itself). No separate membership table like rooms have.
     */
    public function hasMember(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->patient_id === $user->id || $this->submitted_by === $user->id;
    }

    public function applyDiscount(float $amount): float
    {
        if ($this->discount_value <= 0) return $amount;

        return round($amount * (1 - $this->discount_value / 100), 2);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function answers()
    {
        return $this->hasMany(VisitFormAnswer::class)->orderBy('sort_order');
    }

    public function attachments()
    {
        return $this->hasMany(VisitFormAttachment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class);
    }

    public function xrayRequests()
    {
        return $this->hasMany(XrayRequest::class);
    }
}
