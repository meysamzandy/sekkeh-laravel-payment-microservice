<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_id',
        'price',
        'source',
        'selected_gateway',
        'final_gateway',
        'status',
        'transaction_id',
        'error_message'
    ];
}
