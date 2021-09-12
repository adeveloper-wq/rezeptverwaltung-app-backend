<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Steps extends Model{
    protected $table = 'schritte';
    protected $primaryKey = 'S_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'schritt_nr', 'anweisung'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'S_ID', 'R_ID'
    ];

    protected $guarded = [];
}