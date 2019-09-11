<?php

namespace App\Http\Controllers;

use App\Http\Models\SeqScheme;
use App\Http\Models\Transactions;
use Illuminate\Http\Request;



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

    public function createTransaction(Request $req)
    {
        // $this->validate($request, [
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users'
        // ]);
        if (!empty($req->input('asset_id'))) {
            $id_asset       = $req->input('asset_id');
            $snapshot_url   = $req->input('snapshot_url');

            $res_trans = new Transactions;
            $res_seq_scheme = new SeqScheme;

            $arr_store = [
                'asset_id' => $id_asset,
                'result_id' => 1,
                'station_id' => 1,
                'snapshot_url' => $snapshot_url,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
            ];

            $saved = $res_trans->create($arr_store);
            // var_dump($validatedData);
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
            $this->responseCode = 400;
            $this->responseMessage = 'ID Asset tidak dikirim!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }
        

        return response()->json($response, $this->responseCode);
    }

    public function accept(Request $req)
    {
        // $this->validate($request, [
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users'
        // ]);
        if (!empty($req->input('asset_id'))) {
            $id_asset     = $req->input('asset_id');
            $result_id    = $req->input('result_id');
            $station_id   = $req->input('station_id');
            $station_id   = $req->input('station_id');
            $created_by   = $req->input('created_by');

            $res_trans = new Transactions;
            // $res_seq_scheme = new SeqScheme;


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
            $this->responseCode = 400;
            $this->responseMessage = 'ID Asset tidak dikirim!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }


        return response()->json($response, $this->responseCode);
    }

    public function currentStatus(Request $req)
    {
        // $id_transaction = $req->input('transaction_id');
        $id_asset = $req->input('asset_id');
        $transaction = new Transactions;

        $res_trans = Transactions::where('asset_id', $id_asset)
        ->orderBy('created_at', 'desc')
        ->take(1)
        ->get();

        if ($res_trans === null) {
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
