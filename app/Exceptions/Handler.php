<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ErrorHandler\Error\FatalError;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Throwable $exception
     *
     * @return void
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     *
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
      if ($exception instanceof MethodNotAllowedHttpException) {
        return response()->json([
          'message' => 'Method not allowed',
          'status' => false
        ], 405
        );
      }
      if ($exception instanceof NotFoundHttpException) {
        return response()->json([
          'message' => 'Not Found',
          'status' => false
        ], 404
        );
      }
      if ($exception instanceof FatalError) {
        return response()->json([
          'message' => 'Internal server error',
          'status' => false
        ], 500
        );
      }

      return parent::render($request, $exception);
    }
}
