<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username', // <--- Tambah ini
        'password',
        'is_private', // <--- Tambah ini
        'bio', // <--- Tambah ini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Relasi: Siapa yang saya follow?
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Relasi: Siapa yang follow saya?
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Helper untuk cek apakah sudah follow user tertentu
    public function isFollowing($userId)
    {
        return $this->following()->where('following_id', $userId)->where('status', 'accepted')->exists();
    }

    // Relasi: User punya banyak Postingan
    public function posts()
    {
        return $this->hasMany(Post::class)->orderBy('created_at', 'desc');
    }
}
