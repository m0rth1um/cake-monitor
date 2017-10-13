<?php
declare(strict_types = 1);

namespace Monitor\Controller;

use Cake\Controller\Controller;
use Cake\Network\Response;
use Monitor\Lib\MonitorHandler;

class AppController extends Controller
{

    /**
     * Instance of the Monitor Lib
     *
     * @var \Monitor\Lib\MonitorHandler
     */
    protected $_monitor;

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(\Cake\Event\Event $event): void
    {
        $this->_monitor = new MonitorHandler($this->request, $this->response);

        $this->_monitor->handleAuth();

        $this->_monitor->handleChecks();
    }

    /**
     * @param string|null $view   view
     * @param string|null $layout layout
     * @return \Cake\Http\Response
     */
    public function render($view = null, $layout = null): \Cake\Http\Response
    {
        return parent::render($view, $layout);
    }
}
