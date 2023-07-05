<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function order_details()
    {
        return $this->hasMany(Order_details::class);
    }

    public function product()
    {
        return $this->hasMany(Order_details::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
