<?php

namespace App\Http\Controllers;

use App\Http\Models\Assets;
use App\Http\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class AssetsController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];
    private $responseNote = null;

    public function __construct()
    {
        //
    }

    public function index(Request $req)
    {
        $rules['start'] = 'required|integer|min:0';
        $rules['perpage'] = 'required|integer|min:1';

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseStatus = 'Missing Param';
            $this->responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData = $validator->errors();
        } else {
            $res = new Assets();

            $start = $req->input('start');
            $perpage = $req->input('perpage');
            $search = $req->input('search');
            $order = $req->input('order');

            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $sort = $order ?? 'desc';
            $field = 'a.created_at';

            $total = $res->jsonGrid($start, $perpage, $search, true, $sort, $field);
            $resource = $res->jsonGrid($start, $perpage, $search, false, $sort, $field);

            $this->responseCode = 200;
            $this->responseData = $resource;

            $pagination = ['row' => count($resource), 'rowStart' => ((count($resource) > 0) ? ($start + 1) : 0), 'rowEnd' => ($start + count($resource))];
            $this->responseData['meta'] = ['start' => $start, 'perpage' => $perpage, 'search' => $search, 'total' => $total, 'pagination' => $pagination];
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function testDetail($id_asset)
    {
        $res = Assets::find($id_asset)->assetType()->get();
        return $res;
    }

    public function detail(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'qr_code'         => 'required',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $assets = new Assets;
            $qr_code = $req->input('qr_code');

            $res = $assets->getDetail($qr_code);
            if (empty($res)) {
                $this->responseCode = 400;
                $this->responseMessage = 'Asset tidak ditemukan';
            } else {
                $this->responseCode = 200;
                $this->responseData = $res;
                $this->responseNote['pics_url'] = [
                    'url' => '{base_url}/watch/{pics_url}?token={access_token}&un={asset_id}&ctg=assets&src={pics_url}'
                ];
            }
    
            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus, $this->responseNote);
            return response()->json($response, $this->responseCode);
        }

    }

    public function store(Request $req)
    {
        $id_manufacturer        = $req->input('manufacturer_id');
        $id_type_asset          = $req->input('asset_type_id');
        $id_seq_scheme_group    = $req->input('seq_scheme_group_id');
        $validator = Validator::make($req->all(), [
            'asset_type_id'         => [
                'required',
                Rule::exists('asset_type')->where(function ($query) use ($id_type_asset) {
                    $query->where('asset_type_id',  $id_type_asset);
                })
            ],
            'manufacturer_id'       => [
                'required',
                Rule::exists('manufacturer')->where(function ($query) use ($id_manufacturer) {
                    $query->where('manufacturer_id',  $id_manufacturer);
                })
            ],
            'seq_scheme_group_id'   => [
                'required',
                Rule::exists('seq_scheme_group')->where(function ($query) use ($id_seq_scheme_group) {
                    $query->where('seq_scheme_group_id',  $id_seq_scheme_group);
                })
            ],
            'qr_code'               => 'required',
            'serial_number'         => 'required',
            'manufacturer_date'     => 'date_format:d-m-Y',
            'expiry_date'           => 'date_format:d-m-Y',
            'pics_url'              => 'file',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $asset_desc             = $req->input('asset_desc');
            $gross_weight           = $req->input('gross_weight');
            $net_weight             = $req->input('net_weight');
            $pics_url               = $req->input('pics_url');
            $qr_code                = $req->input('qr_code');
            $serial_number          = $req->input('serial_number');
            $manufacture_date       = $req->input('manufacture_date');
            $expiry_date            = $req->input('expiry_date');
            $height                 = $req->input('height');
            $width                  = $req->input('width');
            $from_date              = $req->input('from_date');
            $end_date               = $req->input('end_date');
    
            $temp = Assets::where('serial_number',$serial_number)->get();

            if ($temp->isEmpty()) {
                $user = $req->get('my_auth');
                $res = new Assets;
    
                $res->asset_type_id         = $id_type_asset;
                $res->manufacturer_id       = $id_manufacturer;
                $res->seq_scheme_group_id   = $id_seq_scheme_group;
                $res->asset_desc            = $asset_desc;
                $res->gross_weight          = $gross_weight;
                $res->net_weight            = $net_weight;
                $res->qr_code               = $qr_code;
                $res->serial_number         = $serial_number;
                $res->manufacture_date      = date('Y-m-d', strtotime($manufacture_date));
                $res->expiry_date           = date('Y-m-d', strtotime($expiry_date));
                $res->height                = $height;
                $res->width                 = $width;
                $res->from_date             = date('Y-m-d',strtotime($from_date));
                $res->end_date              = date('Y-m-d', strtotime($end_date));
                $res->created_at            = date('Y-m-d H:i:s');
                $res->created_by            = $user->id_user;
    
                $saved = $res->save();
    
                if (!$saved) {
                    $this->responseCode = 502;
                    $this->responseMessage = 'Data gagal disimpan!';
    
                    $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                } else {
                    $pics_url = $req->file('pics_url')->getClientOriginalName();
                    if ($req->file('pics_url')->isValid()) {
                        $destinationPath = storage_path('app/public') . '/assets/' . $res->asset_id;
                        $req->file('pics_url')->move($destinationPath, $pics_url);

                        $resource = Assets::find($res->asset_id);

                        $resource->pics_url = $pics_url;
                        $resource->save();
                    }


                    $arr_store = [
                        'asset_id' => $res->asset_id,
                        'station_id' => 2,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user->id_user,
                    ];

                    $saved = Transactions::create($arr_store);

                    $this->responseCode = 201;
                    $this->responseMessage = 'Asset berhasil disimpan!';
                    $this->responseData = $req->all();
    
                    $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                }
            } else {
                $this->responseCode = 400;
                $this->responseData = $req->input('serial_number');
                $this->responseMessage = 'Serial Number sudah ada!';
                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
    
            return response()->json($response, $this->responseCode);
        }
    }

    public function delete($id_asset)
    {
        $id_asset = strip_tags($id_asset);

        $destroy = Assets::destroy($id_asset);

        if ($destroy) {
            $this->responseCode = 202;
            $this->responseMessage = 'Asset berhasil dihapus!';
            $this->responseData = $destroy;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Asset gagal dihapus!';
            $this->responseData = $destroy;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }

        return response()->json($response, $this->responseCode);
    }
    
    public function deleteAll()
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $res = Assets::whereNotNull('asset_id');

        $destroy = $res->forceDelete();

        if ($destroy) {
            $responseCode = 202;
            $responseMessage = 'Asset berhasil dihapus semua!';
            $responseData = $destroy;
            $responseStatus = 'Accepted';

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        } else {
            $responseCode = 500;
            $responseMessage = 'Asset gagal dihapus!';
            $responseData = $destroy;

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        }

        return response()->json($response, $responseCode);
    }
}
