<?php

namespace App\Models;

use App\Models\ItemBase;
use App\Models\MaterialItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemBasePrice extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'item_base_id',
        'material_item_code',
        'material_item_id',
        'price',
       
    ];

    public function base()
    {
        return $this->belongsTo(ItemBase::class, 'item_base_id');
    }

    // public function item()
    // {
    //     return $this->belongsTo(MaterialItem::class, 'material_item_id');
    // }

    public function item()
{
    return $this->belongsTo(MaterialItem::class, 'material_item_code', 'code');
}
}
