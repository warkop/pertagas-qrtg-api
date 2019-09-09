<?php

namespace App\Http\Controllers;

use App\Http\Helper\Resp;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Acara;

use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $this->validate($req, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $req->input('username');
        $password = $req->input('password');

        // $data = User::where(['usr_username'=>$username,'usr_pwd'=>$password])->where('aktif', true)->whereNull('deleted_at')->first();
        $data = User::where(['username' => $username])->where('aktif', true)->whereNull('deleted_at')->first();

        $user = new User();


        if (Hash::check($password, $data['usr_pwd'])) {
            // if($data){
            if (is_null($data["usr_token"])) {
                $usr_token = md5("Energeek" . $password . "-token");
                $usr_token = hash("sha1", $usr_token . date("Y-m-d H:i:s"));
                $user->where("usr_id", $data["usr_id"])->update(["usr_token" => $usr_token]);
            } else {
                $usr_token = $data["usr_token"];
            }
            $acara = new Acara;
            $acr = $acara->where('acr_status', '1')->leftJoin('kota', 'kota_id', 'acr_kota_id')->first();
            if (empty($acr)) {
                $data = ["code" => 0, "usr_id" => $data["usr_id"], "usr_role" => $data["usr_role"], "usr_token" => $usr_token, "usr_username" => $data["usr_username"]];
                return (new Resp())->json(
                    200,
                    "Tidak ada acara aktif.",
                    $data
                );
            } else {
                $data = ["code" => 1, "usr_id" => $data["usr_id"], "usr_role" => $data["usr_role"], "usr_token" => $usr_token, "usr_username" => $data["usr_username"], "acr_id" => $acr["acr_id"], "acr_name" => $acr["acr_name"], "acr_kota" => $acr["kota_nama"], "acr_mulai" => date("d-m-Y", strtotime($acr["acr_mulai"])), "acr_selesai" => date("d-m-Y", strtotime($acr["acr_selesai"]))];
                return (new Resp())->json(
                    200,
                    "Berhasil melakukan login.",
                    $data
                );
            }
        } else {
            return (new Resp())->json(
                404,
                "User / Password tidak ditemukan.",
                null
            );
        }
    }
    
    public function logout(Request $req)
    {

    }
}