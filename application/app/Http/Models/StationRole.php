<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class StationRole extends Model
{
    use SoftDeletes;
    protected $table = 'station_role';
    protected $primaryKey = 'station_role_id';

    protected $guarded = [
        'station_role_id',
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
