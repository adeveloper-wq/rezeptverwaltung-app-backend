<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model{
    protected $table = 'gruppen';
    protected $primaryKey = 'name';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'hinweis'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'passwort', 'admin_P_ID', 'HG_Bildpfad', 'menüfarbe'
    ];

    protected $guarded = [];
}