<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoriesStory extends Model
{
    use HasFactory;
    protected $fillable = [
        'stories_id',
        'stories_did',
        'viewes',
        'open_link',
        'subscribers',
    ];
}
