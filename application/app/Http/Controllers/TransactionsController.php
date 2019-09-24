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

    private function processing($station_id, $id_result)
    {
        $res_seq_scheme  = new SeqScheme;

        $obj = $res_seq_scheme->where('predecessor_station_id', $station_id)->where('result_id', $id_result)->first();
        if (!empty($obj)) {
            return $obj->station_id;
        }
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
        $user = $req->get('my_auth');
        $id_asset       = $req->input('asset_id');
        $id_result     = $req->input('result_id');
        $validator = Validator::make($req->all(), [
            'asset_id' => [
                'required',
                Rule::exists('assets')->where(function ($query) use ($id_asset) {
                    $query->where('asset_id',  $id_asset);
                })
            ],
            'result_id' => [
                'required',
                Rule::exists('results')->where(function ($query) use ($id_result) {
                    $query->where('result_id',  $id_result);
                })
            ],
            'snapshot_url' => 'required|mimetypes:image/jpeg,image/png,image/bmp,image/gif|max:9000',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        } else {
            // $res_trans = Transactions::where('asset_id', $id_asset)->orderBy('transaction_id', 'desc')->take(1)->first();
            // $assets = Assets::where('qr_code', $qr_code)->first();
            $res_trans = Transactions::where('asset_id', $id_asset)->orderBy('transaction_id', 'desc')->take(1)->first();
            $seq_scheme = SeqScheme::where('station_id', $user->id_station)
                ->where('predecessor_station_id', $res_trans->station_id)
                ->where('result_id', $res_trans->result_id)
                ->first();

            if (empty($seq_scheme)) {
                $this->responseCode = 500;
                $this->responseMessage = 'Asset tidak sesuai posisi station Anda, silahkan login dengan akun lain atau scan Tabung yang lain!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                if ($res_trans->result_id == 14 || $res_trans->result_id == 15 || $res_trans->result_id == 16 || $res_trans->result_id == 17 || $res_trans->result_id == 18) {
                    if (true) {

                    }
                } else {
                    $station = $this->processing($res_trans->station_id, $res_trans->result_id);

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
                    } else {
                        $snapshot = $req->file('snapshot_url')->getClientOriginalName();
                        if ($req->file('snapshot_url')->isValid()) {
                            $destinationPath = storage_path('app/public') . '/transactions/' . $saved->transaction_id;
                            $req->file('snapshot_url')->move($destinationPath, $snapshot);

                            $temp_trans = Transactions::find($saved->transaction_id);

                            $temp_trans->snapshot_url = $snapshot;
                            $temp_trans->save();
                        }


                        $this->responseCode = 201;
                        $this->responseMessage = 'Data berhasil disimpan!';
                        $this->responseData = $temp_trans;
                    }
                    $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                }

                
            }
        }

        return response()->json($response, $this->responseCode);
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
            $user = $req->get('my_auth');

            $gathel = Transactions::where('asset_id', $id_asset)->orderBy('transaction_id', 'desc')->first();

            if (!empty($gathel)) {
                $seq_scheme = new SeqScheme();
                $res = $seq_scheme->getResultByStation($user->id_station);

                $this->responseCode = 200;
                $this->responseData = $res;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $this->responseCode = 500;
                $this->responseData = $gathel;
                $this->responseMessage = 'Asset tidak ditemukan di transaksi dan station harus ada!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }

            return response()->json($response, $this->responseCode);
        }
    }

    public function currentStatus($id_asset)
    {
        $res_seq_scheme = new SeqScheme;

        $res_trans = $res_seq_scheme->checkPosition($id_asset);
        if (empty($res_trans)) {
            $this->responseCode = 400;
            $this->responseMessage = 'Data tidak ditemukan!';
        } else {
            $this->responseCode = 200;
            $this->responseData = $res_trans;
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);

        return response()->json($response, $this->responseCode);
    }

    public function listTransactionAsset(Request $req)
    {
        $id_asset = $req->input('asset_id');
        $validator = Validator::make($req->all(), [
            'asset_id' => [
                'required',
                Rule::exists('assets')->where(function ($query) use ($id_asset) {
                    $query->where('asset_id',  $id_asset);
                })
            ],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $trans = new Transactions;
            $res = $trans->listTransaction($id_asset);
            $this->responseCode = 200;
            $this->responseData = $res;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }
}
