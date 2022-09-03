<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory, BelongsToUser;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function bankAccount(){
        return $this->hasOne(BankAccount::class);
    }
}
