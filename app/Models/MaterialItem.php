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

    public function basic()
    {
        // This sets up the relationship correctly
        return $this->hasOne(ItemBasePrice::class, 'material_item_code', 'code');
    }

    
    
    public function vendor() {
        return $this->hasOne(Vendor::class,  'code', 'supplier_name');
    }

    
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_favourite_items');
    }
    
}
