<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class DetailAssetStock extends Model
{
    use SoftDeletes;
    protected $table = 'detail_asset_stock';
    protected $primaryKey = 'detail_asset_stock_id';

    protected $guarded = [
        'detail_asset_stock_id',
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
