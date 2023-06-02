<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stories extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'group_id',
        'title',
        'filetype',
        'link',
        'upload_result',
        'active',
    ];
}
