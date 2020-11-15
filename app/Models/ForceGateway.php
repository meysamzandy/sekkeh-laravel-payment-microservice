<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ForceGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'source',
    ];

    /**
     * @return array|string[]
     */
    public function getParams(): array
    {
        return Schema::getColumnListing('force_gateways');
    }
}
