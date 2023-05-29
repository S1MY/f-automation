<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offers extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'for_dev',
        'offer',
        'need',
        'published',
        'votes'
    ];
}
