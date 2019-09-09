<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
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

    public function index(Request $request)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $rules['username'] = 'required';
        $rules['password'] = 'required';
        // $rules['device_id'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $responseCode = 400;
            $responseStatus = 'Missing Param';
            $responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
            $responseData['error_log'] = $validator->errors();
        } else {
            $username = $request->input('username');
            $password = $request->input('password');
            $device_id = $request->input('device');

            $cek_user = User::where('username', $username)->whereNull('deleted_at')->first();

            if ($cek_user) {
                if (Hash::check($password, $cek_user['password'])) {
                    $m_user = User::find($cek_user['user_id']);

                    if (empty($cek_user['token'])) {
                        $access_token = 'dB528-' . rand_str(10) . date('Y') . rand_str(6) . date('m') . rand_str(6) . date('d') . rand_str(6) . date('H') . rand_str(6) . date('i') . rand_str(6) . date('s');

                        $m_user->token = $access_token;
                    } else {
                        $access_token = $cek_user['token'];
                    }

                    $m_user->usr_device_id = $device_id;
                    $m_user->save();

                    $responseCode = 200;
                    $responseData['access_token'] = $access_token;
                    $responseData['role'] = $m_user->role_id;
                    $responseMessage = 'Anda berhasil login';
                } else {
                    $responseCode = 401;
                    $responseMessage = 'Username atau Password Anda salah';
                }
            } else {
                $responseCode = 401;
                $responseMessage = 'Username atau Password Anda salah';
            }
        }

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }
}
