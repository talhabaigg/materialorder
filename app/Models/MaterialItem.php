<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 
        'description', 
        'supplier_name', 
        'costcode', 
        
    ];
}
