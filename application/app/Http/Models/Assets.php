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

    public function getDetail($id_asset)
    {
        $query = DB::table(DB::raw('assets a'))
        ->selectRaw('
        asset_id,
        asset_name,
        at.asset_desc as asset_type_desc,
        a.asset_desc as asset_desc,
        gross_weight,
        net_weight,
        pics_url,
        serial_number,
        TO_CHAR(manufacture_date, \'dd/mm/yyyy\') as manufacture_date,
        TO_CHAR(expiry_date, \'dd/mm/yyyy\') as expiry_date,
        height,
        width,
        a.created_at,
        a.updated_at')
        ->join(DB::raw('asset_type at'), 'a.asset_type_id', '=', 'at.asset_type_id')
        ->where('a.asset_id', $id_asset)
        ->get();

        return $query;
    }
}
