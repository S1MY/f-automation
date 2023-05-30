<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'name',
        'link',
        'avatar',
        'token',
    ];
}
