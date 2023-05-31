<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'name',
        'link',
        'avatar',
        'token',
    ];
    public function User(){
        return $this->belongsTo(User::class, 'id', 'uid');
    }
    public function VkGroup(){
        return $this->hasMany(VkGroup::class, 'account_id', 'id');
    }
}
