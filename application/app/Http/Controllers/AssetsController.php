<?php

namespace App\Http\Controllers;

use App\Http\Models\Assets;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $res = new Assets;

        $responseData = $res->all();
        $responseCode = 200;

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }

    public function store(Request $req)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $id_type_asset          = $req->input('id_type_asset');
        $id_manufacturer        = $req->input('id_manufacturer');
        $id_seq_scheme_group    = $req->input('id_seq_scheme_group');
        $asset_desc             = $req->input('asset_desc');
        $gross_weight           = $req->input('gross_weight');
        $net_weight             = $req->input('net_weight');
        $pics_url               = $req->input('pics_url');
        $serial_number          = $req->input('serial_number');
        $manufacture_date       = $req->input('manufacture_date');
        $expiry_date            = $req->input('expiry_date');
        $height                 = $req->input('height');
        $width                  = $req->input('width');

        $res = new Assets;
        
        $res->asset_type_id         = $id_type_asset;
        $res->manufacturer_id       = $id_manufacturer;
        $res->seq_scheme_group_id   = $id_seq_scheme_group;
        $res->asset_desc            = $asset_desc;
        $res->gross_weight          = $gross_weight;
        $res->net_weight            = $net_weight;
        $res->pics_url              = $pics_url;
        $res->serial_number         = $serial_number;
        $res->manufacture_date      = $manufacture_date;
        $res->expiry_date           = $expiry_date;
        $res->height                = $height;
        $res->width                = $width;

        $saved = $res->save();

        if (!$saved) {
            $responseCode = 502;
            $responseMessage = 'Data gagal disimpan!';

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        } else {
            $responseCode = 201;
            $responseMessage = 'Asset berhasil disimpan!';
            $responseData = $saved;

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        }

        return response()->json($response, $responseCode);
    }

    public function delete(Request $req)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $id_asset = strip_tags($req->input('asset_id'));

        $res = Assets::find($id_asset);

        $destroy = $res->delete();

        if ($destroy) {
            $responseCode = 202;
            $responseMessage = 'Asset berhasil dihapus!';
            $responseData = $destroy;

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        } else {
            $responseCode = 500;
            $responseMessage = 'Asset gagal dihapus!';
            $responseData = $destroy;

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        }

        return response()->json($response, $responseCode);
    }
    
    public function deleteAll()
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $res = Assets::all();

        // foreach ($res as $key) {
            $destroy = $res->forceDelete();
        // }

        if ($destroy) {
            $responseCode = 202;
            $responseMessage = 'Asset berhasil dihapus semua!';
            $responseData = $destroy;

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
