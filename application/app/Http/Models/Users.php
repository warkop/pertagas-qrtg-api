<?php

namespace App\Http\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    use SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'from_date', 'end_date', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    public static function get_auth($username = false)
    {
        if ($username == false) {
            return false;
        }

        $result = DB::table("user")
            ->select(DB::raw('usr_id AS id_user, usr_name AS nama, usr_password AS password, usr_username AS username, usr_role AS role, usr_aktif AS aktif, usr_token_permission AS token_permission, usr_token_limits AS token_limits'))
            ->where('usr_aktif', true)
            ->whereNull('deleted_at');

        $result = $result->where('usr_username', $username)->first();

        return $result;
    }

    public static function get_data($id_user = false, $condition = false)
    {
        $result = DB::table("user")
            ->select(DB::raw('user_id AS id_user, username AS nama, usr_username AS username, usr_role AS role, usr_aktif AS aktif, usr_token_permission AS token_permission, usr_token_limits AS token_limits'))
            ->whereNull('deleted_at');

        if ($condition == true) {
            $result = $result->where('usr_aktif', '=', $condition);
        }

        if ($id_user == true) {
            $result = $result->where('user_id', $id_user)->first();
        } else {
            $result  = $result->orderBy('username', 'ASC')->get();
        }

        return $result;
    }

    public static function getByAccessToken($access_token = false)
    {
        if ($access_token == false) {
            return false;
        }

        $result = DB::table(DB::raw('"users" usr'))
            ->select(DB::raw('user_id AS id_user, username AS username, email,role_id AS role, token AS access_token'))
            ->whereNull(DB::raw("usr.deleted_at"));

        $result = $result->where('token', $access_token);

        $result = $result->first();

        return $result;
    }

    // public function roles()
    // {
    //     return $this->hasOne('App\Http\Models\Roles');
    // }
}
