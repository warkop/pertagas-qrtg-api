<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class SeqScheme extends Model
{
    use SoftDeletes;
    protected $table = 'seq_scheme';
    protected $primaryKey = 'seq_scheme_id';

    protected $guarded = [
        'seq_scheme_id',
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
