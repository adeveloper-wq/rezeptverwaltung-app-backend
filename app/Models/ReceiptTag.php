<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptTag extends Model{
    protected $table = 'receipt_tag';
    public $timestamps = false;
    
    protected $guarded = [];
}