<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'patient_code', 'created_by', 'name', 'description',
        'address', 'discount_value', 'registration_template_id',
        'firebase_room_id', 'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Room $room) {
            if (empty($room->patient_code)) {
                $room->patient_code = self::generateCode();
            }
        });
    }

    private static function generateCode(): string
    {
        do {
            $code = 'PT-' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
        } while (self::where('patient_code', $code)->exists());

        return $code;
    }

    /**
     * Whether the given user may use this room's patient code — either
     * because they are the room's patient, or a member of the room.
     */
    public function hasMember(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->patient_id === $user->id) {
            return true;
        }

        return $this->members()->where('user_id', $user->id)->exists();
    }

    // ── Core relationships ────────────────────────────────────────────────

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrationTemplate()
    {
        return $this->belongsTo(ReportTemplate::class, 'registration_template_id');
    }

    // ── Members ───────────────────────────────────────────────────────────

    public function members()
    {
        return $this->hasMany(RoomMember::class);
    }

    public function doctors()
    {
        return $this->hasMany(RoomMember::class)->where('role', 'doctor')->with('user');
    }

    public function nurses()
    {
        return $this->hasMany(RoomMember::class)->where('role', 'nurse')->with('user');
    }

    public function familyMembers()
    {
        return $this->hasMany(RoomMember::class)->where('role', 'patient_family')->with('user');
    }

    // ── Template assignments ───────────────────────────────────────────────

    public function templateAssignments()
    {
        return $this->hasMany(RoomTemplateAssignment::class)->latest('assigned_at');
    }

    public function activeAssignment()
    {
        return $this->hasOne(RoomTemplateAssignment::class)->whereNull('unassigned_at');
    }

    // ── Reports ───────────────────────────────────────────────────────────

    public function reports()
    {
        return $this->hasMany(RoomReport::class)->latest('submitted_at');
    }

    public function registrationReport()
    {
        return $this->hasOne(RoomReport::class)->where('report_type', 'registration');
    }

    // ── Orders / Lab / Xray (with discount) ──────────────────────────────

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

    // ── Medications & orders ──────────────────────────────────────────────

    public function medications()
    {
        return $this->hasMany(RoomMedication::class);
    }

    public function doctorOrders()
    {
        return $this->hasMany(DoctorOrder::class)->latest();
    }

    // ── Helper ───────────────────────────────────────────────────────────

    public function applyDiscount(float $amount): float
    {
        if ($this->discount_value <= 0) return $amount;

        return round($amount * (1 - $this->discount_value / 100), 2);
    }
}
