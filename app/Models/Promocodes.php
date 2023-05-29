<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocodes extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'users_active',
        'amount',
        'uses',
        'before',
    ];
}
