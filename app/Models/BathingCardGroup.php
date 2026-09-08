<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BathingCardGroup extends Model
{
    use HasFactory, Translatable;

    protected array $translatable = ['name'];

    protected $fillable = ['name', 'name_en', 'unit_price', 'sold_at_point_id'];

    protected $casts = ['unit_price' => 'decimal:2'];

    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class, 'sold_at_point_id');
    }

    public function cards()
    {
        return $this->hasMany(BathingCard::class, 'bathing_card_group_id');
    }
}
