<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id', 'user_id', 'message'];

    // Relasi: pesan milik chat
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    // Relasi: pesan milik user (pengirim)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
