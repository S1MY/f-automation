<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamStory extends Model
{
    use HasFactory;
    protected $fillable = [
        'stream_id',
        'video_did',
        'post_did',
        'views',
        'likes',
        'reposts',
        'comments'
    ];
}
