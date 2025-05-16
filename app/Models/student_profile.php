<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Districtname;

class student_profile extends Model
{
    use HasFactory;
    protected $table = 'sms.student_profile';

    // protected $primaryKey = 'id';

    // public $incrementing = true;

    // protected $keyType = 'int';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'student_pen';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'student_dob' => 'date',
        'last_upd_date' => 'datetime',
        'pres_class' => 'integer',
        'class_id' => 'integer',
        'section_id' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(Schoolmaster::class, 'udise_cd', 'udise_sch_code');
    }
    public function getschool(){
        return $this->hasOne(Schoolmaster::class, 'district_cd', 'district_cd');
    }

    public function getdistrict(){
        return $this->hasOne(Districtname::class,'udise_district_code', 'district_cd');
    }

    public function getFormattedGenderAttribute()
    {
        return match ($this->gender) {
            '1' => 'Male',
            '2' => 'Female',
            '3' => 'Trans.',
            default => 'Unknown',
        };
    }

    /**
     * Get the formatted class name.
     *
     * @return string
     */
    public function getFormattedClassAttribute()
    {
        return match ($this->pres_class) {
            -3 => 'Class Nursery/KG/PP3',
            -2 => 'Class LKG/KG1/PP2',
            -1 => 'Class UKG/KG2/PP1',
            1 => 'Class 1',
            2 => 'Class 2',
            3 => 'Class 3',
            4 => 'Class 4',
            5 => 'Class 5',
            6 => 'Class 6',
            7 => 'Class 7',
            8 => 'Class 8',
            9 => 'Class 9',
            10 => 'Class 10',
            11 => 'Class 11',
            12 => 'Class 12',
            default => 'Unknown',
        };
    }

    /**
     * Get the formatted section.
     *
     * @return string
     */
    public function getFormattedSectionAttribute()
    {
        return match ($this->section_id) {
            0 => 'All Sections',
            1 => 'Section A',
            2 => 'Section B',
            3 => 'Section C',
            4 => 'Section D',
            5 => 'Section E',
            6 => 'Section F',
            7 => 'Section G',
            8 => 'Section H',
            default => 'Unknown',
        };
    }

    /**
     * Get the formatted status.
     *
     * @return string
     */
    public function getFormattedStatusAttribute()
    {
        return match ($this->stud_status) {
            'E' => 'Enrolled',
            'W' => 'Wrong Entry',
            'T' => 'Taken TC',
            'A' => 'Long Absentees',
            'D' => 'Demised',
            'P' => 'Pending',
            'O' => 'Passed Out',
            default => 'Unknown',
        };
    }

    /**
     * Get the formatted category.
     *
     * @return string
     */
    public function getFormattedCategoryAttribute()
    {
        return match ($this->soc_cat_id) {
            '1' => 'SC',
            '2' => 'ST',
            '3' => 'OBC',
            '4' => 'General',
            '5' => 'Others',
            default => 'Unknown',
        };
    }

    /**
     * Get the formatted CWSN status.
     *
     * @return string
     */
    public function getFormattedCwsnAttribute()
    {
        return match ($this->cwsn_yn) {
            '1' => 'Yes',
            '2' => 'No',
            default => 'Unknown',
        };
    }

}
