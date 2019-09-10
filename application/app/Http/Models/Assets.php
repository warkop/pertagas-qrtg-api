<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class Assets extends Model
{
    use SoftDeletes;
    protected $table = 'assets';
    protected $primaryKey = 'asset_id';

    protected $guarded = [
        'asset_id',
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
