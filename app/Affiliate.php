<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'logo', 'website', 'account_id'
    ];

    protected $table = 'affiliates';
}
