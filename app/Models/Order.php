<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function order_items(){
        return $this->hasMany(OrderItem::class);
    }
    public function getNameAttribute(){
        return $this->first_name . ' ' .$this->last_name;
    }

    public function getAdminRevenueAttribute(){
        return $this->order_items()->sum('admin_revenue');
        // return $this->order_items->sum(fn(OrderItem $order_item) => $order_item->admin_revenue);
    }
    public function getAmbassadorRevenueAttribute(){
        return $this->order_items()->sum('ambassador_revenue');
        // return $this->order_items->sum(fn(OrderItem $order_item) => $order_item->admin_revenue);
    }
}
