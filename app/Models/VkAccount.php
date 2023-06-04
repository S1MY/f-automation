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
    public function VkGroup(){
        return $this->hasMany(VkGroup::class, 'account_id', 'id');
    }
    public function StoriesAccount(){
        return $this->hasMany(Stories::class, 'account_id', 'id')->where('group_id', 0)->paginate(1);
    }
}
