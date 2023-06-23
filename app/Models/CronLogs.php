<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronLogs extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'cron_type',
        'item_id',
        'message'
    ];
}
