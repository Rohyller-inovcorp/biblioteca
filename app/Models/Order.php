<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrderItem;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_line',
        'city',
        'postal_code',
        'country',
        'status',
        'total',
        'stripe_session_id',
        'stripe_payment_intent',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
