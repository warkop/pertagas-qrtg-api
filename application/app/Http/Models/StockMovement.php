<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class StockMovement extends Model
{
    use SoftDeletes;
    protected $table = 'stock_movement';
    protected $primaryKey = 'stock_movement_id';

    protected $guarded = [
        'stock_movement_id',
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
