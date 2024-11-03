<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'requisition_id', 'old_status_id', 'new_status_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
