<?php

namespace App\Models;

use App\Models\UserInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramSession extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'username', 'session_id',];

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'instagram_session_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
