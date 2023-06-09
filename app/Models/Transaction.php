<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id', 
        'type',
        'amount', 
        'reference', 
        'method', 
        'status',
        'verified'
    ];

    protected $hidden = [
        'created_at',
        'deleted_at',
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

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

}
