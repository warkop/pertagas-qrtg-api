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

    protected $dateFormat = 'd-m-Y';

    protected $dates = [
        'start_date',
        'end_date',
    ];

    protected $hidden = [
        'asset_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public $timestamps  = false;

    public function ReportType()
    {
        return $this->belongsToMany('App\Http\Models\ReportType', '', '');
    }

    public function getAll()
    {
        $query = DB::table('stock_movement as sm')
        ->select(['stock_movement_id', 'document_number', 's.station_name as station', 'ss.station_name as destination_station'])
        ->join('stations as s', 's.station_id', '=', 'sm.station_id')
        ->join('stations as ss', 'ss.station_id', '=', 'sm.destination_station_id')->get();

        return $query;
    }

    public static function jsonGrid($start, $length, $search = '', $count = false, $sort, $field)
    {
        $result = DB::table('stock_movement as sm')
            ->select([
                'stock_movement_id', 
                'document_number', 
                's.station_id', 
                's.station_name as station', 
                'ss.station_id as destination_id',
                'ss.station_name as destination_station',
                'rt.report_type_id',
                'rt.report_name',
            ])
            ->join('stations as s', 's.station_id', '=', 'sm.station_id')
            ->join('stations as ss', 'ss.station_id', '=', 'sm.destination_station_id')
            ->join('report_type as rt', 'rt.report_type_id', '=', 'sm.report_type_id')
            ->whereNull('sm.deleted_at');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('document_number', 'ILIKE', '%' . $search . '%')
                    ->orWhere('s.station_name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('ss.station_name', 'ILIKE', '%' . $search . '%');
            });
        }

        $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        if ($count == false) {
            return $result;
        } else {
            
            return $result->count();
        }
    }

    public function assetOfStockMovement($id_stock_movement='', $id_asset='')
    {
        $query = DB::table('detail_asset_stock as das')
        ->select([
            'detail_asset_stock_id',
            'das.stock_movement_id',
            'das.asset_id',
            'sm.document_number',
            'a.serial_number',
            'at.asset_name',
        ])
        ->join('stock_movement as sm', 'sm.stock_movement_id', '=', 'das.stock_movement_id')
        ->join('assets as a', 'a.asset_id', '=', 'das.asset_id')
        ->join('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')
        ->where('das.stock_movement_id', $id_stock_movement)
        ->whereNull('das.deleted_at')
        ->get();

        return $query;
    }
}
