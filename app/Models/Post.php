<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'group_id',
        'title',
        'message',
        'link',
        'attachments',
        'active',
    ];
}
