<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;


class tch_profile_update extends model
{
    use HasFactory;
    protected $table = 'sms.tch_profile';
    protected $primaryKey = 'slno';
    public $timestamps = false;

    protected $fillable = [
       'tch_name',
        'mobile_no',
        'ema_il',
        'dob',
    ];
    public function school()
    {
        return $this->belongsTo(SchoolMaster::class, 'udise_sch_code', 'udise_sch_code');
    }
}
