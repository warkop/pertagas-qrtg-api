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
        'report_type_id',
        'station_id',
        'destination_station_id',
        'document_number',
        'ref_doc_number',
        'start_date',
        'end_date',
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
        $result = DB::table('document as d')
            ->select([
                'document_id',
                DB::raw('(CASE 
                    WHEN d.document_status = 1 AND rt.can_be_ref IS NULL THEN document_number
                    WHEN d.document_status = 2 AND rt.can_be_ref IS NULL THEN document_number
                    WHEN d.document_status = 3 AND rt.can_be_ref IS NOT NULL THEN ref_doc_number
                    WHEN d.document_status = 4 AND rt.can_be_ref IS NOT NULL THEN ref_doc_number
                END) as document_number'),
                // 'document_number',
                // 'ref_doc_number', 
                's.station_id', 
                's.station_name as station', 
                'ss.station_id as destination_id',
                'ss.station_name as destination_station',
                'rt.report_type_id',
                'rt.report_name',
                'd.document_status',
                DB::raw('(CASE 
                    WHEN d.document_status = 1 AND rt.can_be_ref IS NULL THEN \'Draft\'
                    WHEN d.document_status = 2 AND rt.can_be_ref IS NULL THEN \'Approve\'
                    WHEN d.document_status = 3 AND rt.can_be_ref IS NOT NULL THEN \'GR Draft\'
                    WHEN d.document_status = 4 AND rt.can_be_ref IS NOT NULL THEN \'GR Approve\'
                END) as status
                '),
            ])
            ->leftJoin('stations as s', 's.station_id', '=', 'd.station_id')
            ->leftJoin('stations as ss', 'ss.station_id', '=', 'd.destination_station_id')
            ->leftJoin('report_type as rt', 'rt.report_type_id', '=', 'd.report_type_id')
            ->whereNull('d.deleted_at');
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

    public function assetOfStockMovement($id_document='', $id_asset='')
    {
        $query = DB::table('stock_movement as sm')
        ->select([
            'd.document_id',
            'sm.stock_movement_id',
            'sm.asset_id',
            'd.document_number',
            'a.qr_code',
            'a.serial_number',
            'at.asset_name',
        ])
        ->join('document as d', 'd.document_id', '=', 'sm.document_id')
        ->join('assets as a', 'a.asset_id', '=', 'sm.asset_id')
        ->join('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')
        ->where('sm.document_id', $id_document)
        ->whereNull('sm.deleted_at')
        ->get();

        return $query;
    }

    public function getDetail($id_document)
    {
        $query = DB::table('document as d')
        ->selectRaw('
            d.document_id,
            d.report_type_id,
            d.station_id,
            d.destination_station_id,
            (select station_name from stations where station_id = d.destination_station_id) destination_name,
            document_number,
            ref_doc_number,
            s.station_name,
            s.abbreviation,
            rt.report_name,
            rt.report_desc,
            TO_CHAR(d.start_date, \'dd-mm-yyyy\') AS start_date,
            TO_CHAR(d.end_date, \'dd-mm-yyyy\') AS end_date
            ')
        ->leftJoin('stations as s', 's.station_id', '=', 'd.station_id')
        ->leftJoin('report_type as rt', 'd.report_type_id', '=', 'rt.report_type_id')
        ->where('d.document_id', $id_document)
        ->whereNull('d.deleted_at')
        ->whereNull('s.deleted_at')
        ->whereNull('rt.deleted_at')
        ->first();

        return $query;
    }
    
    public function getStationRole($id_user)
    {
        $query = DB::table('users as u')
        ->select('user_id',
        'role_id',
        'username',
        'station_name',
        'abbreviation')
        ->join('stations as s', 'u.station_id', '=', 's.station_id')
        ->where('u.user_id', $id_user)
        ->first();

        return $query;
    }

    public function getAssets($start, $length, $search = '', $count = false, $sort, $field, $id_station)
    {
        $result = DB::table('transactions as t')
        ->select('*')
        ->join('assets as a', 't.asset_id', '=', 'a.asset_id')
        ->join('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')
        ->where('t.station_id', $id_station)
        ->whereNull('t.deleted_at')
        ->whereNull('a.deleted_at')
        ->get();

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where('serial_number', 'ILIKE', '%' . $search . '%')
                    ->orWhere('qr_code', 'ILIKE', '%' . $search . '%')
                    ->orWhere('at.asset_name', 'ILIKE', '%' . $search . '%');
            });
        }

        $result  = $result->offset($start)->limit($length)->orderBy($field, $sort);
        if ($count == false) {
            return $result->get();
        } else {
            return $result->count();
        }
    }

    public function listDestination()
    {
        $query = DB::table('stations as s')
        ->select(
	        's.station_id',
            'station_name')
        ->where('station_id', 3)
        ->orWhere('station_id', 2)
        ->get();

        return $query;
    }

    public function listScan($id_document, $status='')
    {
        $query = DB::table('stock_movement as sm')
            ->select([
                'd.document_id',
                'sm.asset_id',
                'd.ref_doc_number',
                'a.serial_number',
                'at.asset_name',
            ])
            ->join('document as d', 'd.document_id', '=', 'sm.document_id')
            ->join('assets as a', 'a.asset_id', '=', 'sm.asset_id')
            ->join('asset_type as at', 'at.asset_type_id', '=', 'a.asset_type_id')
            ->where('sm.document_id', $id_document)
            ->where('sm.stock_move_status', $status)
            ->whereNull('sm.deleted_at')
            ->get();

        return $query;
    }
}
