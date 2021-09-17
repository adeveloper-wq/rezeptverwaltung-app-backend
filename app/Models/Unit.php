<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model{
    protected $table = 'einheitenmenge';
    protected $primaryKey = 'E_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'einheit'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'E_ID'
    ];

    protected $guarded = [];
}