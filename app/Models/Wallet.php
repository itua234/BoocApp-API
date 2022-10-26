<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    use BelongsToUser;

    protected $with = ['bankAccount'];

    protected $fillable = [
        'user_id',
        'balance',
        'available_balance',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'id',
        'user_id'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('updated_at', 'DESC');
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

}
