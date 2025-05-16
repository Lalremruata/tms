<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Std_profile;

class student_attendance extends Model
{
    use HasFactory;

    protected $table = 'sms.student_attendances'; // Specify the schema and table
    protected $fillable = [
        'time',
        'date',
        'teacher_code',
        'status',
        'student_pen',
        'udise_cd',
        'class_id',
        'section_id'
    ];

    public function getstudent()
    {
        return $this->hasOne(Std_profile::class, 'student_pen', 'student_pen');
    }

}
