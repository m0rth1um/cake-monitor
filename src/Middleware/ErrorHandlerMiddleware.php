<?php
declare(strict_types = 1);
namespace Monitor\Middleware;

use Cake\Error\Middleware\ErrorHandlerMiddleware as CoreErrorHandlerMiddleware;
use Monitor\Error\SentryHandler;

class ErrorHandlerMiddleware extends CoreErrorHandlerMiddleware
{

    /**
     * @param \Exception $exception The exception to handle.
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @return \Psr\Http\Message\ResponseInterface A response
     */
    public function handleException(\Exception $exception, \Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $sentryHandler = new SentryHandler();
        $sentryHandler->handle($exception);

        return parent::handleException($exception, $request, $response);
    }
}
