<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentJobs extends Model
{
    use SoftDeletes;

    protected $table = "student_jobs";
    protected $primaryKey = "student_jobs_id";
    protected $dates = ['delete_at'];
}
