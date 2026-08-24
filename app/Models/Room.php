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
        'age', 'gender', 'weight', 'has_allergies', 'allergy_details',
        'marital_status', 'functional_status', 'race', 'education_level', 'blood_group',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'weight'         => 'decimal:2',
        'is_active'      => 'boolean',
        'has_allergies'  => 'boolean',
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

    /**
     * The active nurse/hourly-report template assignment specifically.
     * A room can have an active 'nurse' assignment and an active 'doctor'
     * assignment at the same time (assignTemplate() only replaces
     * same-type assignments), so activeAssignment() alone is ambiguous
     * for any caller that needs the hourly-report template — use this
     * instead wherever the hourly nurse report is involved.
     */
    public function activeNurseAssignment()
    {
        return $this->hasOne(RoomTemplateAssignment::class)
            ->whereNull('unassigned_at')
            ->whereHas('template', fn ($q) => $q->where('template_type', 'nurse'));
    }

    /**
     * The active doctor/monthly-report template assignment specifically —
     * same reasoning as activeNurseAssignment(), for the doctor's report.
     */
    public function activeDoctorAssignment()
    {
        return $this->hasOne(RoomTemplateAssignment::class)
            ->whereNull('unassigned_at')
            ->whereHas('template', fn ($q) => $q->where('template_type', 'doctor'));
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

    // ── Intake — diagnoses, chronic diseases, attachments, notes, complaints ─

    public function diagnoses()
    {
        return $this->belongsToMany(Diagnosis::class, 'room_diagnosis');
    }

    public function chronicDiseases()
    {
        return $this->belongsToMany(ChronicDisease::class, 'room_chronic_disease');
    }

    public function attachments()
    {
        return $this->hasMany(RoomAttachment::class);
    }

    public function doctorNotes()
    {
        return $this->hasMany(DoctorNote::class)->latest();
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class)->latest();
    }

    // ── Helper ───────────────────────────────────────────────────────────

    public function applyDiscount(float $amount): float
    {
        if ($this->discount_value <= 0) return $amount;

        return round($amount * (1 - $this->discount_value / 100), 2);
    }

    /**
     * This user's role within this specific room: the room_members role,
     * or 'patient' when they are the room's patient (who has no
     * room_members row of their own). Null when they're not related to
     * the room at all.
     */
    public function roleOf(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        if ($this->patient_id === $user->id) {
            return 'patient';
        }

        return $this->members()->where('user_id', $user->id)->value('role');
    }

    /**
     * All user ids tied to this room — patient, creator, and every member —
     * the set that should be mirrored into the Firestore room doc's
     * `members` array.
     */
    public function memberUserIds(): array
    {
        return collect([$this->patient_id, $this->created_by])
            ->merge($this->members()->pluck('user_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
