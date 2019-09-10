<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Http\Request;

use DB;

class Users extends Model
{
    use SoftDeletes;
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $guarded = [
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public $timestamps  = false;
}