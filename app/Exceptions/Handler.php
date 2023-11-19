<?php

namespace App\Exceptions;

use App\Helpers\ResponseStatusCodes;
use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @param $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if(stristr($request->getUri(), '/api')){
            $response = $this->handleException($request, $exception);
            //app(CorsService::class)->addActualRequestHeaders($response, $request);
            return $response;
        }
        return parent::render($request, $exception);
    }

    /**
     * @param $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handleException($request, Throwable $exception)
    {
        $responseStatusCodes = ResponseStatusCodes::RESPONSE_STATUS_CODE_1000;

        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1001);
        }
        if ($exception instanceof \BadMethodCallException) {
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1002);
        }
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1003);
        }
        if ($exception instanceof \HttpException) {
            $responseStatusCodes['message'] = $exception->getMessage();
            return $this->errorResponse($responseStatusCodes);
        }
        if ($exception instanceof \HttpResponseException) {
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1004);
        }
        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            $responseStatusCodes['message'] = "Does not exist any $modelName with the specified identification";
            return $this->errorResponse($responseStatusCodes);
        }
        if ($exception instanceof MethodNotAllowedException) {
            return $this->errorResponse("Bad request", 405);
        }
        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1005);
        }
        if ($exception instanceof SuspiciousOperationException) {
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1006);
        }
        if ($exception instanceof TokenMismatchException) {
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1007);
        }
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof QueryException) {
            if ($exception->errorInfo[1] == 1451) {
                return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1009);
            }
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1010);
    }

    /**
     * @param ValidationException $e
     * @param $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if(stristr($request->getUri(), '/api')) {
            $errors = $e->errors();
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1008, $errors);
        }

        return parent::convertValidationExceptionToResponse($e, $request);
    }
}
