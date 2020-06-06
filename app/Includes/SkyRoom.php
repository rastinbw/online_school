<?php

namespace App\Includes;

use App\Models\SkyRoomError;
use Exception;

class Skyroom
{
    const VERSION = '1.1.0';

    const ROOM_STATUS_DISABLED   = 0;
    const ROOM_STATUS_ENABLED    = 1;

    const USER_STATUS_DISABLED   = 0;
    const USER_STATUS_ENABLED    = 1;

    const USER_GENDER_UNKNOWN    = 0;
    const USER_GENDER_MALE       = 1;
    const USER_GENDER_FEMALE     = 2;

    const USER_ACCESS_NORMAL     = 1;
    const USER_ACCESS_PRESENTER  = 2;
    const USER_ACCESS_OPERATOR   = 3;
    const USER_ACCESS_ADMIN      = 4;

    private $http;

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $url    Web service URL
     */
    public function __construct($url)
    {
        require_once 'HttpRequest.php';
        $this->http = new HttpRequest($url);
    }

    public function call($action, $params = array()) {
        $data = array(
            'action' => $action,
            'params' => $params,
        );
        try {
            $result = $this->http->post(json_encode($data));
            if($result['ok'] == false){
                $this->createError($result['error_code'], $result['error_message']);
                return new HttpError($result['error_code'],  $result['error_message']);
            }else return $result;
        } catch (Exception $e) {
            $this->createError($e->getCode(), $e->getMessage());
            return new HttpError($e->getMessage(), $e->getCode());
        }
    }

    public function createError($code, $message){
        $error = new SkyRoomError();
        $error->code = $code;
        $error->message = $message;
        $error->save();
    }
}
