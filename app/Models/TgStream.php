<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgStream extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'channel_id',
        'title',
        'link',
        'active',
        'live',
        'start_count',
        'error_fix'
    ];
}
