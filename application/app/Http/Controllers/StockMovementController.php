<?php

namespace App\Http\Controllers;

use App\Http\Models\StockMovement;
use App\Http\Models\DetailAssetStock;
use App\Http\Models\Stations;
use App\Http\Models\Document;
use App\Http\Models\ReportType;
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
            $field = 'd.created_at';

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
        $temp = Document::where('document_status', 1)->where('station_id', $user->id_station)->first();
        if (empty($temp)) {
            
            $resource = new StockMovement;

            $resource = $resource->getStationRole($user->id_user);

            if (!empty($resource)) {
                $document = new Document;
                $saved = $document->save();
                $id_document = $document->document_id;

                if (!$saved) {
                    $this->responseCode = 500;
                    $this->responseMessage = 'Gagal membuat document number, silahkan ulangi kembali!';

                    $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    return response()->json($response, $this->responseCode);
                } else {
                    $number  = $resource->abbreviation;

                    $angka = 20 - (strlen($number) + strlen($id_document));

                    for ($i = 0; $i < $angka; $i++) {
                        $number .= "0";
                    }

                    $report_type = ReportType::where('has_designation', 1)->first();

                    $number .= $id_document;

                    $document->report_type_id   = $report_type->report_type_id;
                    $document->document_number  = $number;
                    $document->station_id       = $user->id_station;
                    $document->document_status  = 1;
                    $document->created_at       = date('Y-m-d H:i:s');
                    $document->created_by       = $user->id_user;
                    $document->save();

                    $this->responseCode = 200;
                    $this->responseData = [
                        'document_number' => $number,
                        'document_id' => $document->document_id,
                    ];

                    $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    return response()->json($response, $this->responseCode);
                }
            } else {
                $this->responseCode = 500;
                $this->responseMessage = 'Users tidak ditemukan! Silahkan login kembali!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                return response()->json($response, $this->responseCode);
            }

            return response()->json($response, $this->responseCode);
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Anda tidak diizinkan menambah dokumen selama masih ada draft!';
            $this->responseData['document_id'] = $temp->document_id;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function listStockAsset(Request $req)
    {
        $id_document = $req->input('document_id');
        $validator = Validator::make($req->all(), [
            'document_id' => ['required',
            Rule::exists('document')->where(function ($query) use ($id_document) {
                $query->where('document_id',  $id_document);
            })],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $detail_asset_stock = new StockMovement();

            $res = $detail_asset_stock->assetOfStockMovement($id_document);

            $this->responseCode = 200;
            $this->responseData = $res;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function show(Request $req)
    {
        $id_document = $req->input('document_id');

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
            $document = new StockMovement;

            $res = $document->getDetail($id_document);
            if (empty($res)) {
                $this->responseCode = 400;
                $this->responseMessage = 'Document tidak ditemukan';
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
        $id_destination_station     = $req->input('station_id');

        $validator = Validator::make($req->all(), [
            'station_id' => [
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

            $id_document         = $req->input('document_id');
            $start_date         = $req->input('start_date');
            $end_date           = $req->input('end_date');

            $res = Document::find($id_document);

            $res->destination_station_id    = $id_destination_station;
            $res->start_date                = date('Y-m-d', strtotime($start_date));
            $res->end_date                  = date('Y-m-d', strtotime($end_date));
            $res->updated_at                = date('Y-m-d H:i:s');
            $res->updated_by                = $user->id_user;

            $saved = $res->save();

            if (!$saved) {
                $this->responseCode     = 502;
                $this->responseMessage  = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $this->responseCode         = 201;
                $this->responseMessage      = 'Data berhasil disimpan!';
                $this->responseData         = $saved;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
            return response()->json($response, $this->responseCode);
        }
    }

    public function listDestinationStation(Request $req)
    {
        $id_document = $req->input('document_id');
        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
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

    public function accept(Request $req)
    {
        $id_document     = $req->input('document_id');

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
            $user = $req->get('my_auth');
            $id_document         = $req->input('document_id');
            $res = Document::find($id_document);

            $res->document_status = 2;
            $res->save();

            $report_type = ReportType::where('can_be_ref', 1)->first();

            $arr = [
                'ref_doc_number'            => $res->document_number,
                'report_type_id'            => $report_type->report_type_id,
                'station_id'                => $res->station_id,
                'document_status'           => 3,
                'start_date'                => date('Y-m-d', strtotime($res->start_date)),
                'end_date'                  => date('Y-m-d', strtotime($res->end_date)),
                'created_at'                => date('Y-m-d H:i:s'),
                'created_by'                => $user->id_user,
            ];

            $saved = Document::create($arr);

            $stock_movement = StockMovement::where('document_id')->get();

            foreach ($stock_movement as $key) {
                
            }

            if (!$saved) {
                $this->responseCode     = 502;
                $this->responseMessage  = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $this->responseCode         = 201;
                $this->responseMessage      = 'Data berhasil disimpan!';
                $this->responseData         = $saved;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
            return response()->json($response, $this->responseCode);
        }
    }

    public function storeAssets(Request $req)
    {
        $id_document = $req->input('document_id');
        $id_asset = $req->input('asset_id');

        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                })
            ],
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
            $stock_movement = Document::where('document_id', $id_document)->get();
            if (!$stock_movement->isEmpty()) {
                $user = $req->get('my_auth');
                StockMovement::where('document_id', $id_document)->forceDelete();

                $arr = [
                    'document_id'   => $id_document,
                    'asset_id'      => $id_asset,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'created_by'    => $user->id_user,
                ];

                StockMovement::create($arr);

                $this->responseCode = 201;
                $this->responseMessage = 'Data berhasil disimpan!';
                $this->responseData = $arr;
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

    public function deleteAsset(Request $req)
    {
        $id_stock_movement    = $req->input('stock_movement_id');

        $validator = Validator::make($req->all(), [
            'stock_movement_id' => [
                'required',
                Rule::exists('stock_movement')->where(function ($query) use ($id_stock_movement) {
                    $query->where('stock_movement_id',  $id_stock_movement);
                })
            ],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 500;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $resource = StockMovement::find($id_stock_movement);
            $resource->forceDelete();

            $this->responseCode = 202;
            $this->responseMessage = 'Berhasil dihapus!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function delete(Request $req)
    {
        $id_document = $req->input('document_id');
        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                })
            ],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 500;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $destroy = Document::destroy($id_document);

            if ($destroy) {
                $this->responseCode = 202;
                $this->responseMessage = 'Document berhasil dihapus!';
                $this->responseData = $destroy;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $this->responseCode = 500;
                $this->responseMessage = 'Document gagal dihapus! Data mungkin sudah dihapus atau tidak ditemukan';
                $this->responseData = $destroy;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }

            return response()->json($response, $this->responseCode);
        }
    }

    public function deleteAll()
    {
        $stock_movement = StockMovement::whereNotNull('stock_movement_id');
        $destroy2 = $stock_movement->forceDelete();

        $document = Document::whereNotNull('document_id');
        $destroy = $document->forceDelete();

        if ($destroy) {
            $this->responseCode = 202;
            $this->responseMessage = 'Document berhasil dihapus semua!';
            $this->responseData = $destroy;
            $this->responseStatus = 'Accepted';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Document gagal dihapus! Data kemungkinan sudah dihapus semua!';
            $this->responseData = $destroy;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        }

        return response()->json($response, $this->responseCode);
    }
}
