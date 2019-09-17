<?php

namespace App\Http\Controllers;

use App\Http\Models\Assets;
use App\Http\Models\SeqScheme;
use App\Http\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function reduceSize()
    {
        $source = storage_path("app/uploads/{$_GET["id"]}/{$_GET["file"]}");
        if (!is_dir(storage_path("app/uploads/compress/{$_GET["id"]}/"))) {
            mkdir(storage_path("app/uploads/compress/{$_GET["id"]}/"));
        }
        $destination = storage_path("app/uploads/compress/{$_GET["id"]}/{$_GET["file"]}");
        $quality = 90;
        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'asset_id' => 'required',
            'result_id' => 'required',
            'snapshot_url' => 'required|file|max:9000',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }

        $id_asset       = $req->input('asset_id');
        $res_assets     = Assets::find($id_asset);

        if ($res_assets !== null) {
            $user = $req->get('my_auth');
            $id_result     = $req->input('result_id');

            

            $res_trans = Transactions::where('asset_id', $id_asset)->orderBy('created_at', 'desc')->take(1)->first();

            if (empty($res_trans)) {
                $station = 2;
            } else {
                $station = $this->processing($res_trans, $id_result);
            }

            $arr_store = [
                'asset_id' => $id_asset,
                'station_id' => $station,
                'result_id' => $id_result,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $user->id_user,
            ];

            $saved = Transactions::create($arr_store);
            if (!$saved) {
                $this->responseCode = 502;
                $this->responseMessage = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $snapshot = $req->file('snapshot_url')->getClientOriginalName();
                if ($req->file('snapshot_url')->isValid()) {
                    $destinationPath = storage_path('app/public').'/'.$saved->transaction_id;
                    $req->file('snapshot_url')->move($destinationPath, $snapshot);

                    $temp_trans = Transactions::find($saved->transaction_id);

                    $temp_trans->snapshot_url = $snapshot;
                    $temp_trans->save();
                }


                $this->responseCode = 201;
                $this->responseMessage = 'Data berhasil disimpan!';
                $this->responseData = $temp_trans;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'ID Asset tidak ditemukan!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }

        return response()->json($response, $this->responseCode);
    }

    public function processing($res_transaction, $id_result)
    {
        $res_seq_scheme  = new SeqScheme;
        if ($res_transaction->station_id == 2) {
            $obj = $res_seq_scheme->where('predecessor_station_id', $res_transaction->station_id)->where('result_id', $id_result)->first();
            if ($obj !== null) {
                return $obj->station_id;
            }
        }

        if ($res_transaction->station_id == 3) {
            $obj = $res_seq_scheme->where('predecessor_station_id', $res_transaction->station_id)->where('result_id', $id_result)->first();
            if ($obj !== null) {
                return $obj->station_id;
            }
        }

        
        if ($res_transaction->station_id == 4) {
            $obj = $res_seq_scheme->where('predecessor_station_id', $res_transaction->station_id)->where('result_id', $id_result)->first();
            if ($obj !== null) {
                return $obj->station_id;
            }
        }

        if ($res_transaction->station_id == 5) {
            $obj = $res_seq_scheme->where('predecessor_station_id', $res_transaction->station_id)->where('result_id', $id_result)->first();
            if ($obj !== null) {
                return $obj->station_id;
            }
        }

        if ($res_transaction->station_id == 6) {
            $obj = $res_seq_scheme->where('predecessor_station_id', $res_transaction->station_id)->where('result_id', $id_result)->first();
            if ($obj !== null) {
                return $obj->station_id;
            }
        }

        if ($res_transaction->station_id == 7) {
            $obj = $res_seq_scheme->where('predecessor_station_id', $res_transaction->station_id)->where('result_id', $id_result)->first();
            if ($obj !== null) {
                return $obj->station_id;
            }
        }
    }

    public function generateResult(Request $req)
    {
        $id_asset = $req->input('asset_id');
        $validator = Validator::make($req->all(), [
            'asset_id' => ['required',
            Rule::exists('assets')->where(function ($query) use ($id_asset) {
                $query->where('asset_id',  $id_asset);
            })],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $id_station = $req->input('station_id');
            

            if ($id_station == null) {
                $gathel = Transactions::where('asset_id', $id_asset)->get();

                if ($gathel->isEmpty()) {
                    $seq_scheme = new SeqScheme();
                    $res = $seq_scheme->getResultByStation(1);

                    $this->responseCode = 200;
                    $this->responseData = $res;

                    $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                } else {
                $this->responseCode = 500;
                $this->responseData = '';
                $this->responseMessage = 'Asset tidak ditemukan di transaksi dan station harus ada!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                }
            } else {
                $seq_scheme = new SeqScheme();
                $res = $seq_scheme->getResultByStation($id_station);
    
                $this->responseCode = 200;
                $this->responseData = $res;
    
                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }

            return response()->json($response, $this->responseCode);
        }

        
    }

    public function currentStatus($id_asset)
    {
        $res_seq_scheme = new SeqScheme;

        $res_trans = $res_seq_scheme->checkPosition($id_asset);
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
