<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'companyName',
        'keyPerson',
        'contactNo',
        'email',
        'address',
        'customerType',
        'productType',
        'purchasePlan',
        'suggestedModel',
        'date',
        'reference',
        'token',
        'created_by',
    ];

    public function notes()
    {
        return $this->hasMany(Notes::class, 'customer_id');
    }

}
