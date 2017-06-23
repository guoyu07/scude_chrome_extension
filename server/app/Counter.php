<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Counter extends Model
{
    use SoftDeletes;

    protected $table = "counter";
    protected $primaryKey = "counter_id";
    protected $dates = ['delete_at'];

}
