<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class Results extends Model
{
    use SoftDeletes;
    protected $table = 'results';
    protected $primaryKey = 'result_id';

    protected $guarded = [
        'result_id',
    ];

    protected $hidden = [
        'from_date',
        'end_date',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public $timestamps  = false;
}
