<?php

namespace App\Models;

use App\Models\MaterialItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'coordinates',  
        'site_reference', 
        'delivery_contact', 
        'pickup_by', 
        'requested_by', 
        'deliver_to', 
        'notes',
    ];
    public function priceList()
    {
        return $this->hasOne(PriceList::class, 'site_reference', 'site_reference');
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

    public function favouriteMaterials()
    {
        return $this->belongsToMany(MaterialItem::class, 'project_favourite_items');
    }
}
