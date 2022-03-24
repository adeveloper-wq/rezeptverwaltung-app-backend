<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedUser extends Model{
    protected $table = 'banned_users';
    public $timestamps = false;
    protected $primaryKey = "id";
    
    protected $guarded = [];
}