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

    public function showFlow()
    {
        $query = DB::table(DB::raw('seq_scheme ss'))->select(DB::raw('seq_scheme_id,
        scheme_name,
        (SELECT
            station_name FROM stations WHERE stations.station_id = predecessor_station_id)as predecessor_station,
        s.station_name,
        r.result_name,
        ssg.group_name'))
        ->leftJoin(DB::raw('stations s'), 'ss.station_id', '=', 's.station_id')
        ->leftJoin(DB::raw('results r'), 'r.result_id', '=', 'ss.result_id')
        ->leftJoin(DB::raw('seq_scheme_group ssg'), 'ss.seq_scheme_group_id', '=', 'ssg.seq_scheme_group_id')
        ->orderBy('seq_scheme_id')
        ->get();

        return $query;
    }

    public function checkPosition($id_asset)
    {
        $query = DB::table('assets as a')
        ->select(
            't.transaction_id', 
            'a.asset_id', 
            'at.asset_name', 
            's.station_id', 
            't.result_id', 
            'r.result_name', 
            'r.result_desc', 
            'at.asset_desc', 
            's.station_name'
        )
        ->leftJoin('transactions as t', 't.asset_id', '=', 'a.asset_id')
        ->leftJoin('stations as s', 's.station_id', '=', 't.station_id')
        ->leftJoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')
        ->leftJoin('results as r', 't.result_id', '=', 'r.result_id')
        ->where('a.asset_id', $id_asset)
        ->orderBy('transaction_id', 'desc')
        ->take(1)
        ->get();

        return $query;
    }

    public function get_result_by_station($id_station)
    {
        $query = DB::table('seq_scheme as ss')
        ->select('r.result_id', 'r.result_name', 'r.result_desc')
        ->join('results as r', 'ss.result_id', '=', 'r.result_id')
        ->where('predecessor_station_id', $id_station)
        ->groupBy('r.result_id')
        ->get();

        return $query;
    }
}
