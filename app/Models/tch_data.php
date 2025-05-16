<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Schoolmaster;
use App\Models\tch_profile;

class tch_data extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'sms.tch_users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'pass',
        'remember_token',
        'mobile_no',
        'dob',
        'ema_il',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }

    public function getschool()
    {
        return $this->hasMany(Schoolmaster::class, 'udise_sch_code', 'udise_sch_code');
    }

    public function tch_profile()
    {
        return $this->hasMany(tch_profile::class, 'slno', 'slno');
    }
}
