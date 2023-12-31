<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryImage extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'media_id', 'file_path', 'url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
