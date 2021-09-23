<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model{
    protected $table = 'tag';
    protected $primaryKey = 'T_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    /* protected $hidden = [
        'E_ID'
    ]; */

    protected $guarded = [];
}