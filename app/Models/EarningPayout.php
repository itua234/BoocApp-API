<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarningPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'amount', 
        'status',
    ];

    protected $hidden = [
        'created_at',
        'id',
        'referral_id'
    ];

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => number_format($value)
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->toFormattedDateString()
        );
    }

    public function referral()
    {
        return $this->belongsTo(ReferralCode::class, 'referral_id');
    }
}
