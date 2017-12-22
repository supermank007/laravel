<?php

namespace App\Exceptions;

use Exception;

use App\Exceptions\APIException;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    protected function isApiCall(Request $request)
    {
        return strpos($request->getUri(), '/api/') !== false;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $return_json = false;
        if ($this->isApiCall($request) || $request->ajax()) {
            $return_json = true;
        }

        if ($exception instanceof ModelNotFoundException ||
            $exception instanceof NotFoundHttpException) {

            if ($return_json) {
                // ajax 404 json feedback
                return response()->json([
                        'success' => false,
                        'errorType' => class_basename($exception),
                        'message' => 'Resource not found'
                    ], 404);
            } else {
                // normal 404 view page feedback
                return response()->view('errors.404', ['exception' => $exception], 404);
            }


        } elseif ($exception instanceof HttpException) {

            $code = $exception->getStatusCode();

            if ($return_json) {
                return response()->json([
                        'success' => false,
                        'errorType' => class_basename($exception),
                        'message' => $exception->getMessage()
                    ], $code);
            } else {
                return response()->view('errors.error', ['exception' => $exception], $code);
            }
            
        } elseif ($exception instanceof APIException) {
            $code = $exception->getStatusCode();
            return response()->json(['error' => 'Error: ' . $exception->getMessage()], $code);
        }

        if ($return_json) {

            return response()->json([
                    'success' => false,
                    'errorType' => class_basename($exception),
                    'message' => $exception->getMessage()
                ]);

        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
