<?php
declare(strict_types = 1);
namespace Monitor\Error;

use Cake\Error\ErrorHandler as CoreErrorHandler;
use ErrorException;
use Exception;

class ErrorHandler extends CoreErrorHandler
{
    /**
     * Set as the default error handler by CakePHP.
     *
     * @param int $code Code of error
     * @param string $description Error description
     * @param string|null $file File on which error occurred
     * @param int|null $line Line that triggered the error
     * @param array|null $context Context
     * @return bool True if error was handled
     */
    public function handleError(int $code, string $description, ?string $file = null, ?int $line = null, ?array $context = null): bool
    {
        $exception = new ErrorException($description, 0, $code, $file, $line);
        $sentryHandler = new SentryHandler();
        $sentryHandler->handle($exception);

        return parent::handleError($code, $description, $file, $line, $context);
    }

    /**
     * Handle uncaught exceptions.
     *
     * @param \Exception $exception Exception instance.
     * @return void
     * @throws \Exception When renderer class not found
     * @see http://php.net/manual/en/function.set-exception-handler.php
     */
    public function handleException(Exception $exception): void
    {
        $sentryHandler = new SentryHandler();
        $sentryHandler->handle($exception);

        parent::handleException($exception);
    }
}
