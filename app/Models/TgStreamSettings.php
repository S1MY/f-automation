<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgStreamSettings extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'stream_id',
        'type',
        'interval',
        'date',
        'daily'
    ];
}
