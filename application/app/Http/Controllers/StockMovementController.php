<?php

namespace App\Http\Controllers;

use App\Http\Models\StockMovement;
use App\Http\Models\DetailAssetStock;
use App\Http\Models\Stations;
use App\Http\Models\Document;
use App\Http\Models\StationRole;
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
            $res = new StockMovement();

            $start = $req->input('start');
            $perpage = $req->input('perpage');
            $search = $req->input('search');
            $order = $req->input('order');

            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $sort = $order??'desc';
            $field = 'sm.created_at';

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

    public function generateDocumentNumber(Request $req)
    {
        $user = $req->get('my_auth');

        $resource = new StockMovement;

        $resource = $resource->getStationRole($user->role);

        return $resource;

    }

    public function listStockAsset(Request $req)
    {
        $id_stock_movement = $req->input('stock_movement_id');
        $validator = Validator::make($req->all(), [
            'stock_movement_id' => ['required',
            Rule::exists('stock_movement')->where(function ($query) use ($id_stock_movement) {
                $query->where('stock_movement_id',  $id_stock_movement);
            })],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $detail_asset_stock = new StockMovement();

            $res = $detail_asset_stock->assetOfStockMovement($id_stock_movement);

            $this->responseCode = 200;
            $this->responseData = $res;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function show(Request $req)
    {
        $id_stock_movement = $req->input('stock_movement_id');

        $validator = Validator::make($req->all(), [
            'stock_movement_id' => [
                'required',
                Rule::exists('stock_movement')->where(function ($query) use ($id_stock_movement) {
                    $query->where('stock_movement_id',  $id_stock_movement);
                })
            ],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $stock_movement = new StockMovement;

            $res = $stock_movement->getDetail($id_stock_movement);
            if (empty($res)) {
                $this->responseCode = 400;
                $this->responseMessage = 'Stock Movement tidak ditemukan';
            } else {
                $this->responseCode = 200;
                $this->responseData = $res;
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function store(Request $req)
    {
        $id_report_type             = $req->input('report_type_id');
        $id_station                 = $req->input('station_id');
        $id_destination_station     = $req->input('destination_station_id');

        $validator = Validator::make($req->all(), [
            'report_type_id' => [
                'required',
                Rule::exists('report_type')->where(function ($query) use ($id_report_type) {
                    $query->where('report_type_id',  $id_report_type);
                })
            ],
            'station_id' => [
                'required',
                Rule::exists('stations')->where(function ($query) use ($id_station) {
                    $query->where('station_id',  $id_station);
                })
            ],
            'destination_station_id' => [
                'required',
                Rule::exists('stations')->where(function ($query) use ($id_destination_station) {
                    $query->where('station_id',  $id_destination_station);
                })
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

            $ref_doc_number     = $req->input('ref_doc_number');
            $start_date         = $req->input('start_date');
            $end_date           = $req->input('end_date');


            $arr = [
                'ref_doc_number'            => $ref_doc_number,
                'report_type_id'            => $id_report_type,
                'station_id'                => $id_station,
                'destination_station_id'    => $id_destination_station,
                'start_date'                => date('Y-m-d', strtotime($start_date)),
                'end_date'                  => date('Y-m-d', strtotime($end_date)),
                'created_at'                => date('Y-m-d H:i:s'),
                'created_by'                => $user->id_user,
            ];

            $saved = Document::create($arr);

            if (!$saved) {
                $this->responseCode     = 502;
                $this->responseMessage  = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $temp_station = Stations::find($id_station);

                $resource = Document::find($saved->document_id);
                $resource->document_number = $temp_station->abbreviation.date('Ymd').$saved->document_id;
                $resource->save();

                $this->responseCode         = 201;
                $this->responseMessage      = 'Data berhasil disimpan!';
                $this->responseData         = $resource;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
            return response()->json($response, $this->responseCode);
        }
    }

    public function listReadyStation(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'report_type_id' => [
                'required',
                Rule::exists('report_type')->where(function ($query) use ($id_report_type) {
                    $query->where('report_type_id',  $id_report_type);
                })
            ],
            'station_id' => [
                'required',
                Rule::exists('stations')->where(function ($query) use ($id_station) {
                    $query->where('station_id',  $id_station);
                })
            ],
            'destination_station_id' => [
                'required',
                Rule::exists('stations')->where(function ($query) use ($id_destination_station) {
                    $query->where('station_id',  $id_destination_station);
                })
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
        }
    }

    public function storeAssets(Request $req)
    {
        $id_document = $req->input('document_id');
        $temp_asset_id = $req->input('asset_id');

        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                })
            ],
            
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $stock_movement = StockMovement::where('stock_movement_id', $id_stock_movement)->get();
            if (!$stock_movement->isEmpty()) {
                $collection_asset_id = $req->input('asset_id');
                $user = $req->get('my_auth');
                StockMovement::where('stock_movement_id', $id_stock_movement)->forceDelete();

                for ($i = 0; $i < count($collection_asset_id); $i++) {
                    $arr = [
                        'stock_movement_id' => $id_stock_movement,
                        'asset_id' => $collection_asset_id[$i],
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user->id_user,
                    ];

                    StockMovement::create($arr);
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
