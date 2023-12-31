<?php

namespace App\Models;

use App\Models\InstagramSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'instagram_session_id',
        'instagram_id',
        'username',
        'profile_picture_url',
        'bio',
        'media_count',
        'followers_count',
        'following_count',
    ];

    public function instagramSession()
    {
        return $this->belongsTo(InstagramSession::class, 'instagram_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
