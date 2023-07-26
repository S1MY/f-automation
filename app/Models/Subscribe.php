<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'uid',
        'sub_config',
        'sub_old',
        'sub_status',
        'is_test',
        'end_date',
    ];
}
