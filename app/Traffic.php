<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'ip', 'account_id', 'action', 'timestamp',
    ];
    
    //disable eloquent from trying to use updated_at
    public $timestamps = false;
}
