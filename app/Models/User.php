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

    // ── Relationships ─────────────────────────────────────────────────────

    public function roomsAsPatient()
    {
        return $this->hasMany(Room::class, 'patient_id');
    }

    public function roomMemberships()
    {
        return $this->hasMany(RoomMember::class);
    }

    /**
     * The single room this user is currently tied to, either as the
     * room's patient or as a member (doctor/nurse/patient_family).
     * Service bookings and patient-code checks are scoped to this room.
     */
    public function currentRoom(): ?Room
    {
        $room = $this->roomsAsPatient()->where('is_active', true)->latest()->first();

        if ($room) {
            return $room;
        }

        $membership = $this->roomMemberships()
            ->whereHas('room', fn ($q) => $q->where('is_active', true))
            ->with('room')
            ->latest()
            ->first();

        return $membership?->room;
    }

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

    public function visitFormsSubmitted()
    {
        return $this->hasMany(VisitForm::class, 'submitted_by');
    }

    public function visitFormsAsPatient()
    {
        return $this->hasMany(VisitForm::class, 'patient_id');
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
            'university_manager'   => 'مسؤول الجامعة',
            'patient'              => 'مريض',
            'patient_family'       => 'أهل المريض',
            'super_nurse'          => 'ممرض مسؤول',
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
