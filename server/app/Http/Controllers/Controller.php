<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseJsonSuccess($data = ""){
        $data = [
            'code' => 0,
            'data' => $data,
            'msg' => ''
        ];

        return response()->json($data);
    }

    public function responseJsonError($msg = "", $code = 1){
        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => ''
        ];

        return response()->json($data);
    }
}
