<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'image_path', 'caption'];

    // Relasi: Postingan milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}