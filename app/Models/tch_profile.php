<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;


class tch_profile extends model
{
    use HasFactory;
    protected $table = 'sms.tch_profile';
    protected $primaryKey = 'slno';
    public $timestamps = false;

    protected $fillable = [
        'mobile_no',
        'trade',
        'password',
        'tch_name',
        'ema_il'
    ];
    public function school()
    {
        return $this->belongsTo(Schoolmaster::class, 'udise_sch_code', 'udise_sch_code');
    }
}
