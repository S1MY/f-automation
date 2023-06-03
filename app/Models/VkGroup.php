<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'account_id',
        'group_id',
        'name',
        'avatar',
    ];
    public function StoriesGroup(){
        return $this->hasMany(Stories::class, 'group_id', 'id');
    }
}
