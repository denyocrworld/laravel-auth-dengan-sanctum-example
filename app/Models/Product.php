<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['column1', 'column2']; // Replace with appropriate column names

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
