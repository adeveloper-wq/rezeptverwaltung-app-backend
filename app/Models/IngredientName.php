<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientName extends Model{
    protected $table = 'zutatenmenge';
    protected $primaryKey = 'Z_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zutat'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'Z_ID'
    ];

    protected $guarded = [];
}