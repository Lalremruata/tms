<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\student_attendance;

class Std_profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'udise_cd',
        'school_name',
        'district_cd',
        'block_cd',
        'sch_category_id',
        'sch_mgmt_id',
        'student_pen',
        'student_name',
        'gender',
        'student_dob',
        'class_id',
        'section_id',
        'mother_name',
        'father_name',
        'guardian_name',
        'address',
        'pincode',
        'mobile_no_1',
        'mobile_no_2',
        'email_id',
        'mother_tongue',
        'sco_cat_id',
        'minority_id',
        'is_bpl_yn',
        'aay_bpl_yn',
        'ews_yn',
        'cwsn_yn',
        'nat_ind_yn',
        'oosc_mainstreamed_yn',
        'pres_class',
        'stud_status',
        'last_upd_date',
        'userid',
        'aadhaar_no',
        'nationality',
        'apaar_id',
        'remark',
    ];
    protected $casts = [
        'student_pen' => 'string',
    ];

    protected $table = 'sms.student_profile'; // Specify the schema and table

    protected $primaryKey = 'student_pen';

    public function studentAttendances()
    {
        return $this->hasMany(student_attendance::class, 'student_pen', 'student_pen');
    }
    public function school()
    {
        return $this->belongsTo(Schoolmaster::class, 'udise_cd', 'udise_sch_code');
    }

}
