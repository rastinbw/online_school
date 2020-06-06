<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Models\Student;
use App\Includes\Constant;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($code, $result)
    {
        $response = [
            'result_code' => $code,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }

    public function check_token($token){
        if(trim($token) != "")
            return Student::where('token',$token)->first();
        else
            null;
    }

    public function invalid_token_response(){
        return $this->sendResponse(Constant::$INVALID_TOKEN, null);
    }
}
