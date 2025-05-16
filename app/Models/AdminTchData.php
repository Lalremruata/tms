<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\tch_profile;
use App\Models\Schoolmaster;

class AdminTchData extends Model
{
    use HasFactory;

    public $fillable =
    [
        'slno',
        'passw','init','classes','sections',
    ];
    protected $table = 'tch_users';

    public function tch_profile(){
        return $this->hasOne(tch_profile::class,'slno','slno');
    }

    public function school()
    {
        return $this->hasMany(Schoolmaster::class, 'udise_sch_code', 'udise_sch_code');
    }

}
