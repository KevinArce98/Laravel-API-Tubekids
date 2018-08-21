<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Barryvdh\Cors\CorsService;

/* Exceptions */
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
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
        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('Endpoint not found.', Response::HTTP_NOT_FOUND);
        }
        elseif($exception instanceof TokenInvalidException){
            dd($exception);
        }
        elseif($exception instanceof TokenExpiredException){
            dd($exception);
        }
        elseif($exception instanceof JWTException){
            dd($exception);
        }
        elseif($exception instanceof AuthenticationException){
            return $this->errorResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
        elseif($exception instanceof AuthorizationException){
            return $this->errorResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
        elseif ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Data not found for {$model}.", 404);
        }
        elseif ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse("MethodNotAllowedHttpException", $exception->statusCode());
        }
        elseif($exception instanceof ValidationException){
            return $this->errorResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
        return $this->errorResponse($exception->getMessage(), Response::HTTP_NOT_FOUND);
    }
}
    