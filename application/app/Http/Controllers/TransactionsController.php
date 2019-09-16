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

    public function uploadFoto(Request $req, $id_lokasi, $id_berkas, $tipe)
    {
        $transactions = new Transactions;
        $fileName =  $req->file('file')->getClientOriginalName();

        $check = $transactions->where(['id_lokasi' => $id_lokasi, 'id_berkas' => $id_berkas, 'path' => $fileName])->first();
        if (empty($check)) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $ext = strtolower($ext);

            if (in_array($ext, ['jpeg', 'jpg', 'png', 'pdf'])) {
                $path = $req->file('file')->storeAs('uploads/' . $id_lokasi . '/' . $id_berkas, $fileName);
                $transactions->save_data([
                    'id_lokasi' => $id_lokasi,
                    'id_berkas' => $id_berkas,
                    'tipe' => $tipe,
                    'path' => $fileName,
                    'tgl_upload' => date('Y-m-d H:i:s'),
                    'created_by' => $this->userdata()['role'],
                ]);

                return response()->json($path, http_response_code());
            } else {
                return response()->json('Jenis file tidak diizinkan!', http_response_code());
            }
        } else {
            return response()->json('Already has the same file name.', 500);
        }
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
            $id_result     = $req->input('result_id');
            $created_by     = $req->input('created_by');
            $snapshot_url   =  $req->file('snapshot_url')->getClientOriginalName();

            $ext = pathinfo($snapshot_url, PATHINFO_EXTENSION);
            $ext = strtolower($ext);

            // if (in_array($ext, ['jpeg', 'jpg', 'png', 'pdf'])) {
            //     $path = $req->file('file')->storeAs('uploads/' . $id_lokasi . '/' . $id_berkas, $snapshot_url);
            // }

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
                'snapshot_url' => $snapshot_url,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $created_by,
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
        $validator = Validator::make($req->all(), [
            'station_id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $id_station = $req->input('station_id');
            $seq_scheme = new SeqScheme();
            $res = $seq_scheme->get_result_by_station($id_station);

            $this->responseCode = 200;
            $this->responseData = $res;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
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
