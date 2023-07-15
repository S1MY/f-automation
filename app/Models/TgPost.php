<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'channel_id',
        'title',
        'message',
        'link',
        'attachments',
        'start_count',
        'active'
    ];
}
