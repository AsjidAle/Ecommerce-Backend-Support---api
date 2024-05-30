<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponTotal extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'discount', 'valid_till'];
}
