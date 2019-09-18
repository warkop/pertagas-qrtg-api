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

    public function assetType()
    {
        return $this->hasMany('App\Http\Models\AssetType', 'asset_type_id', 'asset_type_id');
    }

    public function getDetail($serial_number)
    {
        $query = DB::table(DB::raw('assets a'))
        ->selectRaw('
        a.asset_id,
        r.result_id,
        s.station_id,
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
        a.from_date,
        a.end_date,
        group_name,
        group_desc,
        COALESCE(s.station_name, \'\')as station_name,
        COALESCE(r.result_name, \'\')as result_name,
        a.created_at,
        a.updated_at')
        ->leftJoin('transactions as t', 't.asset_id', '=', 'a.asset_id')
        ->leftJoin('stations as s', 's.station_id', '=', 't.station_id')
        ->leftJoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')
        ->leftJoin('results as r', 't.result_id', '=', 'r.result_id')
        ->leftJoin('manufacturer as m', 'm.manufacturer_id', '=', 'a.manufacturer_id')
        ->leftJoin('seq_scheme_group as ssg', 'a.seq_scheme_group_id', '=', 'ssg.seq_scheme_group_id')
        ->where('a.serial_number', $serial_number)
        ->whereNull('a.deleted_at')
        ->whereNull('t.deleted_at')
        ->whereNull('s.deleted_at')
        ->whereNull('at.deleted_at')
        ->whereNull('r.deleted_at')
        ->whereNull('m.deleted_at')
        ->whereNull('ssg.deleted_at')
        ->orderBy('transaction_id', 'desc')
        ->take(1)
        ->first();

        return $query;
    }

    public function getAll()
    {
        $query = DB::table('assets as a')
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
        width')
        ->join('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')
        ->whereNull('a.deleted_at')
        ->get();

        return $query;
    }
}
