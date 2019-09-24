<?php

namespace App\Http\Controllers;

use App\Http\Models\StockMovement;
use App\Http\Models\Document;
use App\Http\Models\ReportType;
use App\Http\Models\Assets;
use App\Http\Models\Transactions;
use App\Http\Models\SeqScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StockMovementController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];
    private $responseNote = null;

    private function processing($res_transaction, $id_result)
    {
        $res_seq_scheme  = new SeqScheme;

        $obj = $res_seq_scheme->where('predecessor_station_id', $res_transaction->station_id)->where('result_id', $id_result)->first();
        if ($obj !== null) {
            return $obj->station_id;
        }
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
            $field = 'd.created_at';

            // $total = $res->jsonGrid($start, $perpage, $search, true, $sort, $field);
            $resource = $res->jsonGrid($start, $perpage, $search, false, $sort, $field);

            $this->responseCode = 200;
            $this->responseData = $resource;

            // $pagination = ['row' => count($resource), 'rowStart' => ((count($resource) > 0) ? ($start + 1) : 0), 'rowEnd' => ($start + count($resource))];
            // $this->responseData['meta'] = ['start' => $start, 'perpage' => $perpage, 'search' => $search, 'total' => $total, 'pagination' => $pagination];
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

    public function getReadyAssets(Request $req)
    {
        $user = $req->get('my_auth');

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
                if ($res->station_id == $user->id_station && ($res->result_id == 4 || $res->result_id == 5 || $res->result_id == 6 || $res->result_id == 7 || $res->result_id == 8)) {
                    $this->responseCode = 200;
                    $this->responseData = $res;
                    $this->responseNote['pics_url'] = [
                        'url' => '{base_url}/watch/{pics_url}?token={access_token}&un={asset_id}&ctg=assets&src={pics_url}'
                    ];
                } else {
                    $this->responseCode = 500;
                    $this->responseMessage = 'Station harus dari shipping plant dan kondisinya R1,R2,R3,R4, atau R5';
                }
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus, $this->responseNote);
            return response()->json($response, $this->responseCode);
        }
    }

    public function listStockAsset(Request $req)
    {
        $id_document = $req->input('document_id');
        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                'numeric',
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
                'numeric',
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

    public function listScanned(Request $req)
    {
        $status = $req->input('status');
        $id_document = $req->input('document_id');

        $validator = Validator::make($req->all(), [
            'status' => 'required',
            'document_id'         => [
                'required',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                })
            ]
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            if ($status == 1 or $status == 2) {
                $stock_movement = new StockMovement;

                $res = $stock_movement->listScan($id_document, $status);

                $this->responseCode = 200;
                $this->responseData = $res;
            } else {
                $this->responseCode = 400;
                $this->responseMessage = 'Status parameter tidak dikenali!';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus, $this->responseNote);
            return response()->json($response, $this->responseCode);
        }
        
        
    }

    public function acceptScan(Request $req)
    {
        $user = $req->get('my_auth');
        $id_document = $req->input('document_id');
        $id_asset = $req->input('asset_id');

        $validator = Validator::make($req->all(), [
            'document_id'         => ['required',
            Rule::exists('document')->where(function ($query) use ($id_document) {
                $query->where('document_id',  $id_document);
            })],
            'asset_id'         => ['required',
            Rule::exists('assets')->where(function ($query) use ($id_asset) {
                $query->where('asset_id',  $id_asset);
            })]
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $assets = new Assets;
            
            $rr = $assets->find($id_asset);
            $qr_code = $rr->qr_code;

            $res = $assets->getDetail($qr_code);
            if (empty($res)) {
                $this->responseCode = 500;
                $this->responseMessage = 'Asset tidak ditemukan';
            } else {
                if ($res->station_id == 6 && ($res->result_id == 4 || $res->result_id == 5 || $res->result_id == 6 || $res->result_id == 7 || $res->result_id == 8)) {
                    $jj = StockMovement::where('asset_id', $id_asset)->where('document_id', $id_document)->first();

                    $jj->stock_move_status = 2;
                    $jj->updated_at = date('Y-m-d H:i:s');
                    $jj->updated_by = $user->id_user;
                    $jj->save();

                    $res_doc = Document::find($id_document);

                    // foreach ($stock_movement as $key) {
                        $res_trans = Transactions::where('asset_id', $id_asset)->orderBy('created_at', 'desc')->take(1)->first();
                        // if (empty($res_trans)) {
                        //     $station = 2;
                        // } else {
                        //     $station = $this->processing($res_trans, $res_trans->result_id);
                        // }
                        if ($res_doc->destination_station_id == 3) {
                            $result_status = 10;
                        } else if ($res_doc->destination_station_id == 2) {
                            $result_status = 11;
                        }

                        $arr_store = [
                            'asset_id' => $id_asset,
                            'station_id' => $res_doc->destination_station_id,
                            'result_id' => $result_status,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $user->id_user,
                        ];

                        $saved = Transactions::create($arr_store);
                    // }

                    $this->responseCode = 202;
                    $this->responseData = $jj;
                    $this->responseMessage = 'Asset Berhasil discan';
                } else {
                    $this->responseCode = 500;
                    $this->responseMessage = 'Station harus dari shipping plant dan kondisinya R1,R2,R3,R4, atau R5';
                }
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus, $this->responseNote);
            return response()->json($response, $this->responseCode);
        }
    }

    public function store(Request $req)
    {
        $id_destination_station     = $req->input('station_id');
        $id_document         = $req->input('document_id');

        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                'numeric',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                })
            ],
            'station_id' => [
                'required',
                'numeric',
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
        // $id_document = $req->input('document_id');
        // $validator = Validator::make($req->all(), [
        //     'document_id' => [
        //         'required',
        //         'numeric',
        //         Rule::exists('document')->where(function ($query) use ($id_document) {
        //             $query->where('document_id',  $id_document);
        //         })
        //     ],
        // ]);

        // if ($validator->fails()) {
        //     $this->responseCode = 400;
        //     $this->responseMessage = $validator->errors();

        //     $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        //     return response()->json($response, $this->responseCode);
        // } else {
        $stock_movement = new StockMovement;

        $res = $stock_movement->listDestination();

        $this->responseCode = 200;
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
        // }
    }

    public function accept(Request $req)
    {
        $id_document     = $req->input('document_id');

        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                'numeric',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                    $query->where('document_status',  1);
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
                'destination_station_id'    => $res->destination_station_id,
                'document_status'           => 3,
                'start_date'                => date('Y-m-d', strtotime($res->start_date)),
                'end_date'                  => date('Y-m-d', strtotime($res->end_date)),
                'created_at'                => date('Y-m-d H:i:s'),
                'created_by'                => $user->id_user,
            ];

            $saved = Document::create($arr);

            $stock_movement = StockMovement::where('document_id', $id_document)->get();
            foreach ($stock_movement as $key) {
                $gg = [
                    'document_id'    => $saved->document_id,
                    'asset_id'       => $key->asset_id,
                    'stock_move_status' => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'created_by'     => $user->id_user,
                ];

                StockMovement::create($gg);
            }

            if (!$saved) {
                $this->responseCode     = 502;
                $this->responseMessage  = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                foreach ($stock_movement as $key) {
                    $res_trans = Transactions::where('asset_id', $key->asset_id)->orderBy('created_at', 'desc')->take(1)->first();
                    if (empty($res_trans)) {
                        $station = 2;
                    } else {
                        $station = $this->processing($res_trans, $res_trans->result_id);
                    }
    
                    $arr_store = [
                        'asset_id' => $key->asset_id,
                        'station_id' => $station,
                        'result_id' => $res_trans->result_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user->id_user,
                    ];
    
                    $saved = Transactions::create($arr_store);
                }

                $this->responseCode         = 201;
                $this->responseMessage      = 'Data berhasil disimpan!';
                $this->responseData         = $saved;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            }
            return response()->json($response, $this->responseCode);
        }
    }

    public function approveGR(Request $req)
    {
        $id_document     = $req->input('document_id');
        $user = $req->get('my_auth');
        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                'numeric',
                Rule::exists('document')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                    $query->where('document_status',  3);
                })
            ],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $res = Document::find($id_document);

            $res->document_status = 4;
            $res->updated_at = date('Y-m-d H:i:s');
            $res->updated_by = $user->id_user;

            $saved = $res->save();

            if (!$saved) {
                $this->responseCode     = 502;
                $this->responseMessage  = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $this->responseCode         = 201;
                $this->responseMessage      = 'Data berhasil disimpan!';
                $this->responseData         = $res;

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
                'numeric',
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
            $stock_movement = StockMovement::where('asset_id', $id_asset)->get();
            if ($stock_movement->isEmpty()) {
                $user = $req->get('my_auth');
                $arr = [
                    'document_id'   => $id_document,
                    'asset_id'      => $id_asset,
                    'stock_move_status'      => 1,
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
                $this->responseMessage = 'Asset sudah tidak tersedia! Kemungkinan sudah ditambahkan di stock movement lain';
                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);

                return response()->json($response, $this->responseCode);
            }
        }
    }

    public function deleteAsset(Request $req)
    {
        $id_document    = $req->input('document_id');
        $id_asset    = $req->input('asset_id');

        $validator = Validator::make($req->all(), [
            'document_id' => [
                'required',
                'numeric',
                Rule::exists('stock_movement')->where(function ($query) use ($id_document) {
                    $query->where('document_id',  $id_document);
                })
            ],
            'asset_id' => [
                'required',
                'numeric',
                Rule::exists('stock_movement')->where(function ($query) use ($id_asset) {
                    $query->where('asset_id',  $id_asset);
                })
            ],
        ]);

        if ($validator->fails()) {
            $this->responseCode = 500;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $resource = StockMovement::where('document_id', $id_document)->where('asset_id', $id_asset)->first();
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
                'numeric',
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
