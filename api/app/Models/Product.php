<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'price', 'stock', 'subCategory',
        'brand', 'source', 'sourcePrice',
    ];

    public function image()
    {
        return $this->hasMany(ProductImage::class, 'product');
    }
}
