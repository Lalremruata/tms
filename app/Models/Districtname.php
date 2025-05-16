<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Districtname extends Model
{
    use HasFactory;

    protected $table = 'mizoram115.mst_district'; // Specify the schema and table
    protected $primaryKey = 'udise_district_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function schools()
    {
        return $this->hasMany(Schoolmaster::class, 'district_cd', 'udise_district_code');
    }
}
