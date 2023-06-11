<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoriesSettings extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'stories_id',
        'link_text',
        'link_url',
    ];
}
