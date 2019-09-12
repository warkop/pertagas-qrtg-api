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

    public function checkPosition($id_transaction)
    {
        $query = DB::table(DB::raw('transactions t'))
        ->select('*')
        ->leftJoin(DB::raw('assets a'), 't.asset_id', '=', 'a.asset_id')
        ->leftJoin(DB::raw('stations s'), 's.station_id', '=', 't.station_id')
        ->where('t.transaction_id', $id_transaction)
        ->get();

        return $query;
    }
}
