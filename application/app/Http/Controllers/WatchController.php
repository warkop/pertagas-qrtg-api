<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\User;
use App\Http\Models\Mahasiswa;
use App\Http\Models\MahasiswaFoto;

class WatchController extends Controller
{
    public function
    default($nama, Request $request)
    {
        $access_token    = helpEmpty($request->get("token"), 'null');
        $id_file        = helpEmpty($request->get("un"), 'null');
        $id_parent        = helpEmpty($request->get("prt"), 'null');
        $category    = helpEmpty($request->get("ctg"), 'null');
        $source        = helpEmpty($request->get("src"), 'null');

        $image         = ['.jpg', '.jpeg', '.png'];

        $file = myBasePath('/api/', '/');

        $cek_user = User::get_by_access_token($access_token);

        if (!empty($access_token) && !empty($cek_user)) {
            $cek_id = '';

            if ($category == 'mahasiswa') {
                $cek_id = MahasiswaFoto::get_data($id_file, false, false);
                $id_parent = ($id_parent == md5($cek_id->id_mahasiswa . encText('mahasiswa'))) ? $cek_id->id_mahasiswa : false;

                if (!empty($source) && !empty($category)) {
                    $file .= myStorage($category . '/' . $id_parent . '/' . $source);
                }
            }

            $file = protectPath($file);

            if (file_exists($file) && !is_dir($file)) {
                $type    = 'image';

                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $ext = strtolower($ext);

                if (in_array(strtolower($ext), $image)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($file));
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    ob_clean();
                    flush();
                    readfile($file);
                    exit;
                } else {
                    header('Content-Type:' . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file));
                    header('Content-Length: ' . filesize($file));
                    readfile($file);
                }
            } else {
                $response = helpResponse(404);
                return response()->json($response, 404);
            }
        } else {
            $response = helpResponse(401);
            return response()->json($response, 401);
        }
    }
}
