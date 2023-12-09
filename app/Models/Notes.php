<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    use HasFactory;
    protected $fillable = [
        'note',
        'date',
        'created_by',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by')->select(['id', 'name']);
    }
}
