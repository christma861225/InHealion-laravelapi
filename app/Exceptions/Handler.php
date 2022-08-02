<?php

namespace App\Exceptions;

use Doctrine\DBAL\Query\QueryException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $e
     * @return void
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param $request
     * @param Throwable $e
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if (env("APP_DEBUG")) {
            //return parent::render($request, $e);
        }

        if ($e instanceof HttpException) {
            $message = $e->getMessage() ?: Response::$statusTexts[$e->getStatusCode()];

            return response()->json((
            ['errors' => [
                'status' => $e->getStatusCode(),
                'message' => $message,
            ]
            ]), $e->getStatusCode());
        }

        if ($e instanceof ValidationException) {
            $formattedErrors = [];
            foreach ($e->validator->errors()->getMessages() as $key => $messages) {
                $key = preg_replace('/[a-z]+\./', '', $key);
                $formattedErrors[$key] = array_map(function ($msg) {
                    return preg_replace('/The [a-zA-Z ]+\.([a-z]+) /', '', $msg);
                }, $messages);
            }
            return response()->json(['errors' => $formattedErrors], 422);
        } else if ($e instanceof ApiException) {
            return response()->json(['errors' => [
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]], 500);
        }
        if ($e instanceof Exception && !env('APP_DEBUG')) {
            return response()->json([
                'errors' => [
                    class_basename($e) => $e->getMessage()
                ]
            ], 500);
        }
    }
}
