<?php

namespace App\Models;

use App\Models\MaterialItem;
use Illuminate\Database\Eloquent\Model;

class ItemProjectPrice extends Model
{
    protected $fillable = [
        'price_list',
        'item_code',
        'project_number',
        'price',
       
    ];

    public function item() {
        return $this->hasOne(MaterialItem::class, 'code', 'item_code');
    }

    public function projectlist() {
        return $this->hasOne(Project::class, 'site_reference', 'project_number');
    }
}

