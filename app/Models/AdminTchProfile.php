<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AdminTchData;

class AdminTchProfile extends Model
{
    use HasFactory;

    public $fillable =
    [
        'slno',
        'passw','init','classes','sections',
    ];
    protected $table = 'tch_profile';
    protected $primaryKey = 'slno';

    public function AdminTchData(){
        return $this->hasOne(tch_profile::class,'slno','slno');
    }

}
