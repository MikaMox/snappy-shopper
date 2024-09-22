<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'latitude', 'longitude', 'open', 'type', 'max_delivery_distance'
    ];
    
    public $incrementing = false; 
    protected $keyType = 'string';
    
}
