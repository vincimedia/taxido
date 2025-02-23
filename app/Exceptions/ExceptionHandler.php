<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExceptionHandler extends Exception
{
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report()
    {
        return true;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return $this->apiResponse($this->message, $this->code);
        }

        return $this->webResponse($this->message);
    }

    /**
     * Handle Web response.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function webResponse($message)
    {
        return redirect()->back()->with('error', $message);
    }

    /**
     * Handle API response.
     *
     * @param  \Exception  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiResponse($message, $statusCode)
    {
        $statusCode = $statusCode ?? 500;
        $statusCode = (is_int($statusCode) && ($statusCode > 0 && $statusCode<=500)) ?$statusCode : Response::HTTP_INTERNAL_SERVER_ERROR;
        throw new HttpResponseException(response()->json([
            "message" => $message,
            "success" => false
        ], $statusCode));
    }
}
