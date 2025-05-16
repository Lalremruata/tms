<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schoolmaster extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'udise_sch_code';
    protected $table = 'mizoram115.school_master'; // Specify the schema and table


    public function district()
    {
        return $this->belongsTo(Districtname::class, 'district_cd', 'udise_district_code');
    }

    public function students()
    {
        return $this->hasMany(Std_profile::class, 'udise_cd', 'udise_sch_code');
    }

    public function teachers()
    {
        return $this->hasMany(tch_profile::class, 'udise_sch_code', 'udise_sch_code');
    }
}
