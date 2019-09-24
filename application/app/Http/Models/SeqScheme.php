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
            's.station_name',
            'a.serial_number',
            'a.gross_weight',
            'a.net_weight',
            'a.pics_url',
            'a.from_date',
            'a.end_date',
            'a.height',
            'a.width'
        )
        ->leftJoin('transactions as t', 't.asset_id', '=', 'a.asset_id')
        ->leftJoin('stations as s', 's.station_id', '=', 't.station_id')
        ->leftJoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')
        ->leftJoin('results as r', 't.result_id', '=', 'r.result_id')
        ->leftJoin('manufacturer as m', 'm.manufacturer_id', '=', 'a.manufacturer_id')
        ->leftJoin('seq_scheme_group as ssg', 'a.seq_scheme_group_id', '=', 'ssg.seq_scheme_group_id')
        ->where('a.asset_id', $id_asset)
        ->orderBy('transaction_id', 'desc')
        ->take(1)
        ->first();

        return $query;
    }

    public function getResultByStation($station_id)
    {
        // $query = DB::table('seq_scheme as ss')
        // ->select('r.result_id', 'r.result_name', 'r.result_desc','station_name', 'ss.station_id')
        // ->join('results as r', 'ss.result_id', '=', 'r.result_id')
        // ->join('stations as s', 'ss.station_id', '=', 's.station_id')
        // ->where('ss.station_id', $station_id)
        // ->where('predecessor_station_id', $predecessor_id)
        // ->where('ss.result_id', $result_id)
        // ->groupBy('r.result_id', 's.station_name', 'ss.station_id')
        // ->get();

        $query = DB::table('results as r')
        ->select('r.result_id', 'r.result_name', 'r.result_desc')
        // ->join('results as r', 'ss.result_id', '=', 'r.result_id')
        // ->join('stations as s', 'ss.station_id', '=', 's.station_id')
        ->where('r.station_id', $station_id)
        // ->where('predecessor_station_id', $predecessor_id)
        // ->where('ss.result_id', $result_id)
        // ->groupBy('r.result_id', 's.station_name', 'ss.station_id')
        ->get();

        return $query;
    }
}
