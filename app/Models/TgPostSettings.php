<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgPostSettings extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'post_id',
        'remove_id',
        'type',
        'interval',
        'date',
        'daily'
    ];
}
