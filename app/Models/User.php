<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name', 'phone', 'email', 'role',
        'fcm_token', 'date_of_birth', 'gender',
        'patient_code', 'related_patient_id', 'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active'     => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if ($user->role === 'patient' && empty($user->patient_code)) {
                $user->patient_code = self::generatePatientCode();
            }
        });
    }

    private static function generatePatientCode(): string
    {
        do {
            $code = 'PT-' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
        } while (self::where('patient_code', $code)->exists());

        return $code;
    }

    // ── Relationships ─────────────────────────────────────────────────────

    public function relatedPatient()
    {
        return $this->belongsTo(User::class, 'related_patient_id');
    }

    public function familyMembers()
    {
        return $this->hasMany(User::class, 'related_patient_id');
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function nursingRequests()
    {
        return $this->hasMany(NursingRequest::class);
    }

    public function bathingRequests()
    {
        return $this->hasMany(BathingRequest::class);
    }

    public function careRequests()
    {
        return $this->hasMany(CareRequest::class);
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class);
    }

    public function xrayRequests()
    {
        return $this->hasMany(XrayRequest::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'doctor'               => 'طبيب',
            'nurse'                => 'ممرض',
            'head_nurse'           => 'رئيس الممرضين',
            'university_manager'   => 'مسؤول الجامعة',
            'patient'              => 'مريض',
            'patient_family'       => 'أهل المريض',
            default                => $this->role,
        };
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'male'   => 'ذكر',
            'female' => 'أنثى',
            default  => '—',
        };
    }
}
