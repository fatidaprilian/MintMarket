<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'store_id'];

    // Relasi: chat punya banyak pesan
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Relasi: chat milik user (pembeli)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: chat milik toko, pastikan ada model Store.php
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
