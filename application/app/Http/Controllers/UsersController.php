<?php

namespace App\Http\Controllers;

use App\Http\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
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
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $res = new Users;

        $responseData = $res->all();
        $responseCode = 200;

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }

    public function changePassword(Request $req)
    {
        
        $id_user = $req->input('user_id');
        $password = $req->input('password');
        $new_password = $req->input('new_password');
        $retype_password = $req->input('retype_password');

        $user = Users::findOrFail($id_user);

        $validator = Validator::make($req->all(), [
            'user_id' => ['required',
                Rule::exists('users')->where(function ($query) use ($id_user) {
                    $query->where('user_id',  $id_user);
                })
            ],
            'password' => [
                'required'
            ],
            'new_password' => 'required',
            'retype_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseMessage = $validator->errors();

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            if (Hash::check($password, $user->password)) {
                $user->fill([
                    'password' => Hash::make($new_password)
                ])->save();

                $this->responseCode = 200;
                $this->responseMessage = 'Password berhasil diubah';
                $this->responseData = [];

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                return response()->json($response, $this->responseCode);
            } else {
                $this->responseCode = 500;
                $this->responseMessage = 'Password lama tidak cocok!';
                $this->responseData = [];

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                return response()->json($response, $this->responseCode);
            }
        }
    }
}
