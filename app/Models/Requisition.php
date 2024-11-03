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
        'supplier_name', 
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

            $requisition->created_by = auth()->id();
        });
        static::updating(function ($requisition) {
            // Set updated_by to the currently authenticated user's ID
            $requisition->updated_by = auth()->id();
        
            // Capture the original and new status of `is_processed`
            $oldStatus = $requisition->getOriginal('is_processed');
            $newStatus = $requisition->is_processed;
        
            // Log activity if `is_processed` has changed
            if ($oldStatus != $newStatus) {
                RequisitionActivity::create([
                    'requisition_id' => $requisition->id,
                    'old_status_id' => $oldStatus,
                    'new_status_id' => $newStatus,
                    'user_id' => auth()->id(),
                ]);
            }
        });
        
        
    }

    
   

    public function lineItems()
    {
        return $this->hasMany(RequisitionLineItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updator()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    public function projectsetting()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function activities() {
        return $this->hasMany(RequisitionActivity::class, 'requisition_id');
    }
    // public function activities(): HasMany
    // {
    //     return $this->hasMany(TicketActivity::class, 'ticket_id', 'id');
    // }
}
