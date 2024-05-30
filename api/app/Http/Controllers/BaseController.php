<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    public function sendResponse($result, $message = "", $code = 200)
    {
        $response['data'] = $result;

        if($message){
            $response['message'] = $message;
        }
        
        return response()->json($response,$code);
    }

    public function sendError($error, $message = [], $code = 401)
    {
        $response['message'] = $error;
        
        if(!empty($message)){
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }
}
