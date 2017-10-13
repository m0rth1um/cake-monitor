<?php
declare(strict_types = 1);
namespace Monitor\Error;

use Cake\Core\Configure;
use Throwable;

class SentryHandler
{
    /**
     * @var \Raven_Client $_ravenClient
     */
    protected $_ravenClient = null;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $options = [
            'processors' => [
                'Raven_Processor_SanitizeDataProcessor'
            ],
            'processorOptions' => [
                'Raven_Processor_SanitizeDataProcessor' => [
                    'fields_re' => '/(' . implode('|', Configure::read('CakeMonitor.Sentry.sanitizeFields')) . ')/i'
                ]
            ]
        ];
        if (is_callable(Configure::read('CakeMonitor.Sentry.sanitizeExtraCallback'))) {
            $options['processors'][] = '\Monitor\Lib\SanitizeCallbackDataProcessor';
            $options['processorOptions']['\Monitor\Lib\SanitizeCallbackDataProcessor'] = [
                'callback' => Configure::read('CakeMonitor.Sentry.sanitizeExtraCallback')
            ];
        }
        $this->_ravenClient = new \Raven_Client(Configure::read('CakeMonitor.Sentry.dsn'), $options);
    }

    /**
     * Throwable Handler
     *
     * @param Throwable $throwable Throwable to handle
     * @return void|bool
     */
    public function handle(Throwable $throwable): ?bool
    {
        if (!Configure::read('CakeMonitor.Sentry.enabled') || error_reporting() === 0) {
            return false;
        }

        $errorHandler = new \Raven_ErrorHandler($this->_ravenClient);
        $errorHandler->registerShutdownFunction();
        $errorHandler->handleException($throwable);
    }

    /**
     * Capture a message via sentry
     *
     * @param string $message The message (primary description) for the event.
     * @param array $params params to use when formatting the message.
     * @param array $data Additional attributes to pass with this event (see Sentry docs).
     * @param bool $stack Print stack trace
     * @param null $vars Variables
     * @return bool
     */
    public function captureMessage(string $message, array $params = [], array $data = [], bool $stack = false, $vars = null): bool
    {
        if (!Configure::read('CakeMonitor.Sentry.enabled') || error_reporting() === 0) {
            return false;
        }

        return $this->_ravenClient->captureMessage($message, $params, $data, $stack, $vars);
    }
}
