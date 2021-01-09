<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'factor_hash',
        'price',
        'source',
        'selected_gateway',
        'final_gateway',
        'status',
        'transaction_id',
        'error_message',
        'alias'
    ];

    /**
     * @return array|string[]
     */
    public function getParams(): array
    {
        return Schema::getColumnListing('transaction_logs');
    }
}
