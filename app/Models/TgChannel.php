<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgChannel extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'account_id',
        'name',
        'title',
    ];
}
