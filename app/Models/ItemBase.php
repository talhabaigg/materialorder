<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemBase extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'effective_from',
        'effective_to'
    ];

    public function price()
    {
        return $this->hasOne(ItemBasePrice::class);
    }
}
