<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'name',
        'site_reference'
    ];

    public function projectprice() {
        return $this->hasMany(ItemProjectPrice::class, 'price_list', 'name');
    }
    public function project()
{
    return $this->belongsTo(Project::class, 'site_reference', 'site_reference');
}
}
   

