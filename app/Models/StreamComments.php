<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamComments extends Model
{
    use HasFactory;
    protected $fillable = [
        'stream_id',
        'stream_story_id',
        'time',
        'name',
        'avatar',
        'link',
        'comment',
    ];
}
