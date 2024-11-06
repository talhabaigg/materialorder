<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionLineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'item_code',
        'description',
        'qty',
        'cost',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    protected $casts = [
        'cost' => 'float',
    ];
        
}
