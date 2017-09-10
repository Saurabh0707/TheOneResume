<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Traits\ApiResponser;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Collection;
use Illuminate\Http\Concerns\InteractsWithContentTypes;
//use Guzzle\Http\Exception\ClientException;
class Handler extends ExceptionHandler
{
    use ApiResponser;
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        else if ($exception instanceof ModelNotFoundException) {
            $modelname= $exception->getModel();
            return $this->errorResponse("This {$modelname} does not exist", 404);
        }else if($exception instanceof AuthenticationException){
            return $this->unauthenticated( $request, $exception);
        }else if($exception instanceof AuthorisationException){
            return $this->errorResponse($exception->getmessage(),403);
        }else if($exception instanceof NotFoundHttpException){
            return $this->errorResponse('URL not found',403);
        }else if($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse('Method not allowed',404);
        }else if($exception instanceof TokenMismatchException){
            return redirect()->back()->withInput();
        }else if($exception instanceof QueryException){
            $errorcode = $exception->errorInfo[1];
            if($errorcode=1451)//similarly other 
            return $this->errorResponse('Cannot Remove This Resource Permanently, Other resources are related to it.',409);
        }
        //     else if($exception instanceof ClientException)
        // {
        //     dd($exception);
        // }
        if(config('app.debug')){
            return parent::render($request, $exception);//if app is in production debug is set to true. otherwise it is set to false            
        }
        return $this->errorResponse('Unexpected Exception. Try later', 500);//if not in production mode then this response will go
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
        if($this->isFrontend($request))
        {
            return redirect()->guest('login');
        }
        if ($request->expectsJson()) {
            return $this->errorResponse(['Unauthenticated.'], 401);
        }
     }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();  
        if($this->isFrontend($request))  
        {
            return $request->ajax()? response()->json($errors, 422): redirect()->back()->withInput($request->input())->withErrors($errors);
        }    
        return $this->errorResponse($errors, 422);
    }

    protected function isFrontend($request)
    {
        return $request->acceptsHtml()&&collect($request->route()->middleware())->contains('web');
    }
}
