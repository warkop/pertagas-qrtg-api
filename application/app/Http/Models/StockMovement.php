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
            ->select(['stock_movement_id', 'document_number', 's.station_name as station', 'ss.station_name as destination_station'])
            ->join('stations as s', 's.station_id', '=', 'sm.station_id')
            ->join('stations as ss', 'ss.station_id', '=', 'sm.destination_station_id')
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
}
