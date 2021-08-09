<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model{
    protected $table = 'zugehörigkeit';
    public $timestamps = false;
    
    protected $guarded = [];
}