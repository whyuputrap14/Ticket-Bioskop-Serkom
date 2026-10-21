<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id',
        'starts_at',
        'hall',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
