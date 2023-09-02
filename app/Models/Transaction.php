<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id', 'address', 'payment', 'total_price', 'shipping_price', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transactions_id', 'id');
    }

    public static function boot()
    {
      parent::boot();
      self::creating(function ($model) {
        $formatId = "INV".date('dmy')."-";
        $maxValue = DB::table('transactions')->where('invoice_number','like',"$formatId%")->max('invoice_number');
        if ($maxValue != null) {
          $counter = (int) substr($maxValue,10,3) + 1;
          $model->invoice_number = $formatId.str_pad($counter,3,0,STR_PAD_LEFT);
        }else{
          $model->invoice_number = $formatId.'001';
        }
      });
    }
}
