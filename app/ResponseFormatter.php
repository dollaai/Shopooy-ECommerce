<?php

namespace App;

use Illuminate\Support\MessageBag;

class ResponseFormatter
{
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => [],
            'validation' => null,
            'response_date' =>[]
        ],
        'data' => null,
    ];

    public static function success($data = null, $message = [])
    {
        self::$response['meta']['message'] = $message;
        self::$response['meta']['response_date'] = now()->format('Y-m-d H:i:s');
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function error($code = null,$message = [],  $validations = null)
    {
        if ($message instanceof MessageBag) {
            $validations = $message->toArray();  // taruh detail di validation
            $message = "Validation Error";       // kasih pesan umum
        }
        self::$response['meta']['code'] = $code;
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['message'] = $message;
        self::$response['meta']['validation'] = $validations;
        self::$response['meta']['response_date'] = now()->format('Y-m-d H:i:s');

        return response()->json(self::$response, self::$response['meta']['code']);
    }
}