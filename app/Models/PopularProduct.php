<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopularProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'products_id', 'volume'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id', 'id')->with(['galleries'=> function ($query) {
            $query->where('main_image', 1)->select(['products_id','url','main_image']);
        }])->select(['id','name','price']);
    }
}
