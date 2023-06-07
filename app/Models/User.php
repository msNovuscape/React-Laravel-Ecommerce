<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function scopeAmbassador($query){
        return $query->where('is_admin',0);
    }

    public function orders(){
        return $this->hasMany(Order::class)->where('complete', 1);
    }

    public function getRevenueAttribute(){
        return $this->orders->sum(function ($order) {
            return $order->ambassador_revenue;
        });
    }

    public function getNameAttribute(){
        return $this->first_name . ' ' .$this->last_name;
    }
}
