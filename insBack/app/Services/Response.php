<?php

namespace App\Services;

class Response
{
    const HTTP_USER_NOT_FOUND = 425;
    const HTTP_TOKEN_INVALID = 498;
    const HTTP_TOKEN_REQUIRED = 499;
    const HTTP_SERVER_ERROR = 500;
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;

    public const RESPONSE = ['status' => null, 'message' => null, 'data' => null];

    public static function error($message)
    {
        $res = ['status'=>'error','message'=>$message,'data'=>null];
        return response()->json($res, self::HTTP_SERVER_ERROR);
    }

    public static function success($message, $data)
    {
        return self::makeResponse('success', $message, $data, self::HTTP_OK);
    }

    private static function makeResponse($status, $message, $data, $statusCode)
    {
        $res = ['status' => $status, 'message' => $message, 'data' => $data];
        return response()->json($res, $statusCode);
    }
}

