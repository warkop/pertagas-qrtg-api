<?php

namespace App\Http\Controllers;

use App\Http\Models\Assets;
use App\Http\Models\SeqScheme;
use App\Http\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TransactionsController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];
    public function __construct()
    {
        //
    }

    public function index()
    {
        $res = new Transactions;

        return $res->all();
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'asset_id' => 'required',
            'result_id' => 'required',
            // 'file' => 'required|file|max:9000',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }

        $id_asset       = $req->input('asset_id');
        $res_assets = Assets::find($id_asset);
        if ($res_assets !== null) {
            // $snapshot_url   = $req->input('snapshot_url');
            $created_by     = $req->input('created_by');

            $res_trans = Transactions::where('asset_id', $id_asset)->orderBy('created_at', 'desc')->take(1)->get();

            if ($res_trans->isEmpty()) {
                $station = 2;
            } else {
                
            }

            $arr_store = [
                'asset_id' => $id_asset,
                // 'station_id' => 2,
                // 'station_id' => 2,
                // 'snapshot_url' => $snapshot_url,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by,
            ];

            $saved = $res_trans->create($arr_store);
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
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'ID Asset tidak ditemukan!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }
        

        return response()->json($response, $this->responseCode);
    }

    public function accept(Request $req)
    {
        $this->validate($req, [
            'asset_id' => 'required',
            'transaction_id' => 'required'
        ]);

        $id_transaction     = $req->input('transaction_id');
        $id_asset     = $req->input('asset_id');
        $result_id    = $req->input('result_id');
        $station_id   = $req->input('station_id');
        $station_id   = $req->input('station_id');
        $created_by   = $req->input('created_by');

        $res_trans = new Transactions;

        $res_assets = Assets::find($id_asset);
        $res_transaction = Transactions::find($id_transaction);

        if ($res_assets !== null) {
            if ($res_transaction !== null) {
                $this->processing($res_transaction);

                $res_trans->transaction_id = $id_transaction;
                $res_trans->asset_id = $id_asset;
                $res_trans->result_id = $result_id;
                $res_trans->station_id = $station_id;
                $res_trans->created_at = date('Y-m-d H:i:s');
                $res_trans->created_by = $created_by;

                $saved = $res_trans->save();
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
            } else {
                $this->responseCode = 500;
                $this->responseMessage = 'ID Transaksi tidak ditemukan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'ID Asset tidak ditemukan!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }

        return response()->json($response, $this->responseCode);
    }

    public function processing(Request $req)
    {
        $id_transaction  = $req->input('transaction_id');
        $res_transaction = Transactions::find($id_transaction);
        $res_seq_scheme  = new SeqScheme;

        if ($res_transaction->station_id == 2) {
            $obj = $res_seq_scheme->where('predecessor_station_id', null)->where('station_id', 2)->where('result_id', $res_transaction->result_id)->get();
            if ($obj !== null) {
                if ($res_transaction->result_id === null) {
                    $res_transaction->result_id = 2;
                    $res_transaction->station_id = 3;
                    $res_transaction->updated_at = date('Y-m-d H:i:s');
                    $res_transaction->updated_by = 1;
                    $saved = $res_transaction->save();

                    if (!$saved) {
                        $this->responseCode = 502;
                        $this->responseMessage = 'Tahap 1 gagal dilalui! Ada yang salah saat memproses di database!';

                        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    } else {
                        $this->responseCode     = 200;
                        $this->responseMessage  = 'Tahap 1 berhasil!';
                        $this->responseData     = $res_transaction;
    
                        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    }

                    return response()->json($response, $this->responseCode);
                }
            }
        }
        // var_dump($res_transaction->result_id);
        if ($res_transaction->station_id == 3) {
            $res_seq_scheme->where('predecessor_station_id', 2)->where('station_id', 1)->where('result_id', $res_transaction->result_id)->get();
            if ($res_seq_scheme !== null) {
                if ($res_transaction->result_id == 2) {
                    // return 'phase : 2';

                    $res_transaction->result_id = 2;
                    $res_transaction->updated_at = date('Y-m-d H:i:s');
                    $res_transaction->updated_by = 1;
                    $saved = $res_transaction->save();

                    if (!$saved) {
                        $this->responseCode = 502;
                        $this->responseMessage = 'Tahap 2 gagal dilalui! Ada yang salah saat memproses di database!';

                        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    } else {
                        $this->responseCode     = 200;
                        $this->responseMessage  = 'Tahap 2 berhasil!';
                        $this->responseData     = $res_transaction;

                        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    }

                    return response()->json($response, $this->responseCode);
                }
            }
        }

        
        if ($res_transaction->station_id == 3) {
            $res_seq_scheme->where('predecessor_station_id', 2)->where('station_id', 1)->where('result_id', $res_transaction->result_id)->get();
            if ($res_transaction->predecessor_station == 2) { 
                if ($res_transaction->result_id == 1) {
                    // return 'phase : 3';
                    $res_transaction->result_id = 3;
                    $res_transaction->updated_at = date('Y-m-d H:i:s');
                    $res_transaction->updated_by = 1;
                    $saved = $res_transaction->save();

                    if (!$saved) {
                        $this->responseCode = 502;
                        $this->responseMessage = 'Tahap 2 gagal dilalui! Ada yang salah saat memproses di database!';

                        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    } else {
                        $this->responseCode     = 200;
                        $this->responseMessage  = 'Tahap 2 berhasil!';
                        $this->responseData     = $res_transaction;

                        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    }

                    return response()->json($response, $this->responseCode);
                }
            }
        }

        if ($res_transaction->station_id == 3) {
            if ($res_transaction->predecessor_station == 2) { 
                if ($res_transaction->result_id == 1) {
                    return 'phase : 4';
                }
            }
        }
        
    }

    public function currentStatus(Request $req)
    {
        $id_transaction = $req->input('transaction_id');
        // $id_asset = $req->input('asset_id');
        $res_seq_scheme = new SeqScheme;

        $res_trans = $res_seq_scheme->checkPosition($id_transaction);
        if ($res_trans->isEmpty()) {
            $this->responseCode = 400;
            $this->responseMessage = 'Data tidak ditemukan!';
        } else {
            $this->responseCode = 200;
            $this->responseData = $res_trans;
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);

        return response()->json($response, $this->responseCode);
    }
}
