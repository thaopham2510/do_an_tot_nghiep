<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function shipping(){
        return $this->belongsTo(Shipping::class);
    }
    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class);
    }

    public function phieu_xuat(){
        return $this->hasOne(PhieuXuat::class);
    }
}
