<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class Transactions extends Model
{
    use SoftDeletes;
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';

    protected $guarded = [
        'transaction_id',
    ];

    protected $hidden = [
        'from_date',
        'end_date',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public $timestamps  = false;

    public function getTransactionAsset($id_asset)
    {
        $result = DB::table(DB::raw('transactions'))
            ->select(DB::raw('*'))
            ->where(DB::raw('asset_id'), '=', $id_asset)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        return $result;
    }
}
