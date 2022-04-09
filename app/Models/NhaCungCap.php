<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    use HasFactory;

    public function phieu_nhaps(){
        return $this->hasMany(PhieuNhap::class);
    }
}
