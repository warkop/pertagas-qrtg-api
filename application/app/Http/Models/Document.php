<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class Document extends Model
{
    use SoftDeletes;
    protected $table = 'document';
    protected $primaryKey = 'document_id';

    protected $guarded = [
        'document_id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public $timestamps  = false;
}
