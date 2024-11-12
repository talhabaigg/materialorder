<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'file_path',
        'original_file_name',
        'mime_type',
        'file_size',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
