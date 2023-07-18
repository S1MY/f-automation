<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'uid',
        'email',
        'balance',
        'partner',
        'newsletter',
        'partners_clicks',
        'vk_account_places',
        'vk_group_places',
        'tg_channel_places',
    ];
}
