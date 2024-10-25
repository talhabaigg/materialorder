<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisition extends Model
{
    use HasFactory, SoftDeletes;
    

    protected $fillable = [
        'date_required', 
        'pickup_time',
        'supplier_id', 
        'project_id', 
        'site_reference', 
        'delivery_contact', 
        'pickup_by', 
        'requested_by', 
        'deliver_to', 
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($requisition) {
            // Get the last requisition number, if any
            $maxId = Requisition::withTrashed()->max('id');
            
            // Increment the maxId for the new requisition number
            $nextNumber = $maxId ? $maxId + 1 : 1; // If no requisition exists, start with 1

            // Set the requisition_number using the incremented id
            $requisition->requisition_number = 'REQ-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function lineItems()
    {
        return $this->hasMany(RequisitionLineItem::class);
    }
}
