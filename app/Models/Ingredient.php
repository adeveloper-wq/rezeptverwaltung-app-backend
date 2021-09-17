<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model{
    protected $table = 'zutaten';
    protected $primaryKey = 'ZR_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menge', 'E_ID', 'Z_ID'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'ZR_ID', 'R_ID'
    ];

    protected $guarded = [];
}