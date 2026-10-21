<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $guarded = ['id'];

    // Relasi balik ke model Film
    public function film()
    {
        return $this->belongsTo(Film::class, 'film_id');
    }

    // Relasi balik ke model Order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
}
