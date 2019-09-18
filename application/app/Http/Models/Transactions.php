<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class Transactions extends Model
{
    use SoftDeletes;
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';

    protected $guarded = [
        'transaction_id',
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

    public function getTransactionAsset($id_asset)
    {
        $result = DB::table(DB::raw('transactions'))
            ->select(DB::raw('*'))
            ->where(DB::raw('asset_id'), '=', $id_asset)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        return $result;
    }

    public function listTransaction($id_asset)
    {
        $query = DB::table('transactions as t')
        ->select(
            't.transaction_id',
            't.asset_id',
            't.result_id',
            'asset_name',
            'at.asset_desc as asset_type_desc',
            'a.asset_desc as asset_desc',
            'station_name',
            'result_name',
            'result_desc',
            'snapshot_url'
        )
        ->leftJoin('assets as a', 't.asset_id', '=', 'a.asset_id')
        ->leftJoin('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')
        ->leftJoin('results as r', 't.result_id', '=', 'r.result_id')
        ->leftJoin('stations as s', 't.station_id', '=', 's.station_id')
        ->where('t.asset_id', $id_asset)
        ->orderBy('t.created_at', 'desc')
        ->get();

        return $query;
    }
}
