<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'domain', 'store_url', 'email'
    ];

    public static function fromDomain($domain)
    {
        return static::whereDomain($domain)->first();
    }
}
