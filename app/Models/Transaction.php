<?php

namespace App\Models;

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

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }

}
