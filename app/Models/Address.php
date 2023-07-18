<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Resources\City;
use App\Models\Resources\County;

class Address extends Model
{
    use HasFactory;

    //protected $table = 'addresses';

    protected $primaryKey = 'address_id';

    protected $guarded = ['address_id'];
}
