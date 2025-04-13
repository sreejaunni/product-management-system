<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'shipping_address',
        'order_date',
    ];

    /**
     * Define the relationship between Order and User.
     * An order belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship between Order and OrderItem.
     * An order can have many order items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Define the relationship between Order and Product through OrderItem.
     * An order can have many products.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')->withPivot('quantity', 'price');
    }

    /**
     * Scope to get only completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get only pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
