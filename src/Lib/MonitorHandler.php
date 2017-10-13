<?php
declare(strict_types = 1);
namespace Monitor\Lib;

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Network\Response;

/**
 * Used for processing monitor checks
 */
class MonitorHandler
{

    /**
     * Configuration that is used by the methods of this class
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Reference of the current request object
     *
     * @var \Cake\Network\Http\Request
     */
    public $request;

    /**
     * Reference of the current response object
     *
     * @var \Cake\Network\Http\Response
     */
    public $response;

    /**
     * Constructor
     *
     * @param Request  $request  Current Request
     * @param Response $response Current Response
     */
    public function __construct(Request &$request, Response &$response)
    {
        $this->_config = Configure::read('CakeMonitor');
        $this->_validateConfig();

        $this->request =& $request;
        $this->response =& $response;
    }

    /**
     * Validates Config
     *
     * @throws \Exception if configuration is incomplete
     * @return void
     */
    protected function _validateConfig(): void
    {
        foreach ($this->_config as $key => $value) {
            if (!isset($value)) {
                throw new \Exception('Incomplete configuration: ' . $key, 1);
            }
        }
    }

    /**
     * Handle authentication by header token
     *
     * @return void
     */
    public function handleAuth(): void
    {
        if ($this->request->header('CAKEMONITORTOKEN') !== $this->_config['accessToken']) {
            die('NOT AUTHENTICATED');
        }
    }

    /**
     * Handle all defined checks
     *
     * @return void
     */
    public function handleChecks(): void
    {
        $errors = [];
        foreach ($this->_config['checks'] as $name => $check) {
            if (empty($check)) {
                continue;
            }
            $result = $check['callback']();
            if ($result !== true) {
                $errors[] = $name . ': <br>' . $check['error'] . ' - ' . $result;
            }
        }
        if (!empty($errors)) {
            $this->response->statusCode(500);

            echo date('Y-m-d H:i:s') . ': ' . $this->_config['projectName'] . ' - ' . $this->_config['serverDescription'] . ' - Status Code: ' . $this->response->statusCode() . '<br><br> ';
            foreach ($errors as $error) {
                echo $error . '<br><br>';
            }
            die;
        }
        $this->_config['onSuccess']();
        die;
    }
}
