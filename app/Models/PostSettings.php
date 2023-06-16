<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSettings extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'post_id',
        'friends_only',
        'type',
        'interval',
        'date',
        'daily'
    ];
}
