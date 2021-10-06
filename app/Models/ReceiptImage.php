<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptImage extends Model{
    protected $table = 'receipt_image';
    public $timestamps = false;
    
    protected $guarded = [];
}