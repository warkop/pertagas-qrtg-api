<?php

namespace App\Http\Controllers;

use App\Http\Models\Assets;
use App\Http\Models\SeqScheme;
use App\Http\Models\StationRole;
use App\Http\Models\StockMovement;
use App\Http\Models\Transactions;
use App\Http\Models\Document;
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
                Rule::exists('results')->where(function ($query) use ($id_result, $user) {
                    $query->where('result_id',  $id_result)
                    ->where('station_id',  $user->id_station);
                })
            ],
            'snapshot_url' => 'required|mimetypes:image/jpeg,image/png,image/bmp,image/gif|max:9000',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();
        } else {
            $res_trans = Transactions::where('asset_id', $id_asset)->orderBy('transaction_id', 'desc')->take(1)->first();
            $seq_scheme = SeqScheme::where('station_id', $user->id_station)
                ->where('predecessor_station_id', $res_trans->station_id)
                ->where('result_id', $res_trans->result_id)
                ->first();

            if (empty($seq_scheme)) {
                $this->responseCode = 500;
                $this->responseMessage = 'Tabung tidak sesuai posisi station Anda, silahkan login dengan akun lain atau scan Tabung yang lain!';
            } else {
                if ($user->id_station != $res_trans->station_id) {
                    $res_document = Document::where('document_status', 4)->where('destination_station_id', $user->id_station)->orderBy('document_id', 'desc')->first();
                    if (!empty($res_document)) {
                        $res_stock_movement = StockMovement::where('document_id', $res_document->document_id)->where('asset_id', $id_asset)->where('stock_move_status', 2)->first();
                        if (!empty($res_stock_movement)) {
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
                                    $this->responseMessage = 'Data berhasil disimpan!';
                                } else {
                                    $this->responseMessage = 'Data berhasil disimpan, tapi file yang Anda unggah tidak ikut tersimpan karena tidak valid';
                                }

                                $this->responseCode = 201;
                                $this->responseData = $temp_trans;
                            }
                        } else {
                            $this->responseCode = 500;
                            $this->responseMessage = 'Tabung tidak ikut discan pada Good Receive! Silahkan buat ulang Stock Movement dari awal!';
                        }
                    } else {
                        $this->responseCode = 500;
                        $this->responseMessage = 'Tabung belum masuk Good Receive atau Good Receive dari Tabung bersangkutan belum di setujui!';
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
                            $this->responseMessage = 'Data berhasil disimpan!';
                        } else {
                            $this->responseMessage = 'Data berhasil disimpan, tapi file yang Anda unggah tidak ikut tersimpan karena tidak valid';
                        }

                        $this->responseCode = 201;
                        $this->responseData = $temp_trans;
                    }
                }
            }
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
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
                $this->responseMessage = 'Tabung tidak dapat diidentifikasi!';

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
