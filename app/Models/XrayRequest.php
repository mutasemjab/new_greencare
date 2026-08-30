<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class XrayRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'patient_code', 'address_id', 'room_id', 'visit_form_id',
        'booking_date', 'booking_time', 'notes', 'total', 'status', 'result_file',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total'        => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function visitForm()
    {
        return $this->belongsTo(VisitForm::class);
    }

    public function tests()
    {
        return $this->hasMany(XrayRequestTest::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'بانتظار التأكيد',
            'confirmed'   => 'مؤكد',
            'in_progress' => 'قيد التنفيذ',
            'completed'   => 'مكتمل',
            'cancelled'   => 'ملغي',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'warning',
            'confirmed'   => 'info',
            'in_progress' => 'primary',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }

    public function getResultFileUrlAttribute(): ?string
    {
        return $this->result_file ? Storage::disk('public')->url($this->result_file) : null;
    }

    public function scopeWithResults($query)
    {
        return $query->whereNotNull('result_file');
    }
}
