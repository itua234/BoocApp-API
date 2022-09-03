<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'bank_code',
        'wallet_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'bank_code',
        'wallet_id',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
