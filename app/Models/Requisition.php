<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_required', 
        'supplier_id', 
        'project_id', 
        'site_reference', 
        'delivery_contact', 
        'pickup_by', 
        'requested_by', 
        'deliver_to', 
        'notes',
    ];

    public function lineItems()
    {
        return $this->hasMany(RequisitionLineItem::class);
    }
}
