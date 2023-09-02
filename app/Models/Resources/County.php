<?php

namespace App\Models\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    use HasFactory;

    protected $table = 'counties';

    protected $primaryKey = 'county_id';

    protected $guarded = ['county_id'];
}
