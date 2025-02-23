<?php

namespace Modules\TwoFactor\SMS;

use Exception;
use App\Helpers\Helpers;

class TwoFactor
{

  public static function getIntent($message)
  {

    $api_key = env('TWOFACTOR_API_KEY');

    $url = 'https://2factor.in/API/V1/' . $api_key . '/SMS/' . $message["to"] . '/' . $message["body"] . '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    $response = curl_exec($ch);
    $err = curl_error($ch);
    $message_res = json_decode($response);
    curl_close($ch);
    if (!empty($err)) {
      throw new Exception($err, 500);
    }

    return $message_res;
  }
}
