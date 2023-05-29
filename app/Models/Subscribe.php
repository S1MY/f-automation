<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'sub_config',
        'sub_status',
        'sub_alt',
        'end_date',
    ];
}
