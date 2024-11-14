<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected static function booted(): void
{
    static::creating(function ($vendor) {
        // Set created_by to the currently authenticated user's ID, if available
        if ($userId = auth()->id()) {
            $vendor->created_by = $userId;
        }
    });

    static::updating(function ($vendor) {
        // Set updated_by to the currently authenticated user's ID, if available
        if ($userId = auth()->id()) {
            $vendor->updated_by = $userId;
        }
    });

   
}
public function creator() {
    return $this->belongsTo(User::class, 'created_by');
}
public function items() {
    return $this->hasMany(MaterialItem::class, 'supplier_name');
}
}