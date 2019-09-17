<?php

namespace App\Http\Controllers;

use App\Http\Models\StockMovement;
use App\Http\Models\DetailAssetStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StockMovementController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function __construct()
    {
        //
    }

    private function generateRandomString()
    {
        for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {
            $randomNumber .= mt_rand(0, 11);
        }

        return 'STA'. $randomNumber;
    }

    public function index(Request $req)
    {
        $search     = $req->input('search');
        $length     = $req->input('length');
        $sort       = $req->input('sort');
        $order      = $req->input('order');

        $res = new StockMovement;

        return $res->getAll();
    }

    public function store(Request $req)
    {
        $temp_report_id = $req->input('report_type_id');
        $temp_station_id = $req->input('station_id');
        $temp_destination_id = $req->input('destination_id');

        $validator = Validator::make($req->all(), [
            'report_type_id' => [
                'required',
                Rule::exists('report_type')->where(function ($query) use ($temp_report_id) {
                    $query->where('report_type_id',  $temp_report_id);
                })
            ],
            'station_id' => [
                'required',
                Rule::exists('stations')->where(function ($query) use ($temp_station_id) {
                    $query->where('station_id',  $temp_station_id);
                })
            ],
            'destination_id' => [
                'required'
            ],
            'start_date'    => 'date_format:d-m-Y',
            'end_date'      => 'date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {

            $user = $req->get('my_auth');

            $id_report_type     = $req->input('report_type_id');
            $id_station         = $req->input('station_id');
            $ref_doc_number     = $req->input('ref_doc_number');
            $id_destination     = $req->input('destination_id');
            $start_date         = $req->input('start_date');
            $end_date           = $req->input('end_date');

            $arr_store = [
                'station_id' => $id_station,
                'report_type_id' => $id_report_type,
                'destination_station_id' => $id_destination,
                'ref_doc_number' => $ref_doc_number,
                'start_date' => date('Y-m-d', strtotime($start_date)),
                'end_date' => date('Y-m-d', strtotime($end_date)),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->id_user,
            ];

            $saved = StockMovement::create($arr_store);



            if (!$saved) {
                $this->responseCode = 502;
                $this->responseMessage = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $stock_movement = StockMovement::find($saved->stock_movement_id);
                $stock_movement->document_number = 'STA'.date('Ymd').$saved->stock_movement_id;
                $stock_movement->save();

                $this->responseCode = 201;
                $this->responseMessage = 'Data berhasil disimpan!';
                $this->responseData =  $stock_movement;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
            return response()->json($response, $this->responseCode);
        }
    }

    public function storeAssets(Request $req, $id_stock_movement)
    {
        $stock_movement = StockMovement::where('stock_movement_id', $id_stock_movement)->get();
        if (!$stock_movement->isEmpty()) {
            $collection_asset_id = $req->input('asset_id');
            $user = $req->get('my_auth');
            DetailAssetStock::where('stock_movement_id', $id_stock_movement)->forceDelete();

            for ($i = 0; $i < count($collection_asset_id); $i++) {
                $arr = [
                    'stock_movement_id' => $id_stock_movement,
                    'asset_id' => $collection_asset_id[$i],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user->id_user,
                ];

                DetailAssetStock::create($arr);
            }

            $this->responseCode = 201;
            $this->responseMessage = 'Data berhasil disimpan!';
            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);

            return response()->json($response, $this->responseCode);
        } else {
            $this->responseCode = 400;
            $this->responseMessage = 'Stock Movement tidak ditemukan!';
            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);

            return response()->json($response, $this->responseCode);
        }

        
    }

    public function delete($id_stock_movement)
    {
        $id_stock_movement = strip_tags($id_stock_movement);
        $destroy = StockMovement::destroy($id_stock_movement);

        if ($destroy) {
            $this->responseCode = 202;
            $this->responseMessage = 'Stock Movement berhasil dihapus!';
            $this->responseData = $destroy;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Stock Movement gagal dihapus! Data mungkin sudah dihapus atau tidak ditemukan';
            $this->responseData = $destroy;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }

        return response()->json($response, $this->responseCode);
    }

    public function deleteAll()
    {
        $assets = StockMovement::whereNotNull('stock_movement_id');
        $destroy = $assets->forceDelete();

        $detail_asset_stock = DetailAssetStock::whereNotNull('detail_asset_stock_id');
        $destroy2 = $detail_asset_stock->forceDelete();

        if ($destroy && $destroy2) {
            $this->responseCode = 202;
            $this->responseMessage = 'Stock Movement berhasil dihapus semua!';
            $this->responseData = $destroy;
            $this->responseStatus = 'Accepted';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Stock Movement gagal dihapus! Data kemungkinan sudah dihapus semua!';
            $this->responseData = $destroy;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }

        return response()->json($response, $this->responseCode);
    }
}
