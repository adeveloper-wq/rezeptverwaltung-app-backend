<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model{
    protected $table = 'rezeptdaten';
    protected $primaryKey = 'R_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titel', 'portionen', 'kochzeit', 'vegan', 'vegetarisch', 'herzhaft', 'süß', 'suppe', 'party', 'getränk', 'backen'. 'kochen', 'basic'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'G_ID', 'P_ID'
    ];

    protected $guarded = [];
}