<?php

namespace App\Http\Controllers;

use App\Http\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockMovementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $res = new StockMovement;

        return $res->all();
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'asset_id' => 'required',
            'document_number' => 'required',
            'report_id' => 'required',
            'station_id' => 'required',
            'destination_id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $id_report_type         = $req->input('report_type_id');
            $id_asset           = $req->input('asset_id');
            $id_station         = $req->input('station_id');
            $document_number    = $req->input('document_number');
            $ref_doc_number     = $req->input('ref_doc_number');
            $id_destination     = $req->input('destination_id');
            $start_date         = $req->input('start_date');
            $end_date           = $req->input('end_date');

            $arr_store = [
                'asset_id' => $id_asset,
                'station_id' => $id_station,
                'repor_type_id' => $id_report_type,
                'destination_id' => $id_destination,
                'document_number' => $document_number,
                'ref_doc_number' => $ref_doc_number,
                'start_date' => date('Y-m-d', strtotime($start_date)),
                'end_date' => date('Y-m-d', strtotime($end_date)),
                'created_at' => date('Y-m-d H:i:s'),
                // 'created_by' => $created_by,
            ];

            $saved = Transactions::create($arr_store);
            if (!$saved) {
                $this->responseCode = 502;
                $this->responseMessage = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $this->responseCode = 201;
                $this->responseMessage = 'Data berhasil disimpan!';
                $this->responseData = $saved;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
        }
    }
}
