<?php
namespace Simcify\Exceptions;

use Exception;
use Pecee\Handlers\IExceptionHandler;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Pecee\SimpleRouter\Exceptions\HttpException;

class Handler implements IExceptionHandler {

    /**
     * Handle an error that occurs when routing has began
     * 
     * @param   \Pecee\Http\Request $request
     * @param   \Exception          $error
     * @return  mixed
     */
	public function handleError(Request $request, Exception $error) {
		/* The router will throw the NotFoundHttpException on 404 */
		if($error instanceof NotFoundHttpException) {
            
            $request->setRewriteUrl(url('/404'));
			return $request;

        }
        if ($error instanceof HttpException && isset(explode('not allowed', $error->getMessage())[1]) ) {
            $request->setRewriteUrl(url('/405'));
			return $request;
        }

		throw $error;

	}

}
