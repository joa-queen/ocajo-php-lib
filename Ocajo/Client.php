<?php

namespace Ocajo;

use Exception;

class Client
{
    private $api_key;
    private $config;

    private static $instance;

    private static $endpoint = 'http://api.ocajo.com/';

    private static $warnings = array(
        \E_NOTICE            => 'Notice',
        \E_STRICT            => 'Strict',
        \E_USER_WARNING      => 'User Warning',
        \E_USER_NOTICE       => 'User Notice',
        \E_DEPRECATED        => 'Deprecated',
        \E_WARNING           => 'Warning',
        \E_USER_DEPRECATED   => 'User Deprecated',
        \E_CORE_WARNING      => 'Core Warning',
        \E_COMPILE_WARNING   => 'Compile Warning',
        \E_RECOVERABLE_ERROR => 'Recoverable Error'
    );

    protected static $fatals = array(
        \E_ERROR         => 'Error',
        \E_PARSE         => 'Parse',
        \E_COMPILE_ERROR => 'Compile Error',
        \E_CORE_ERROR    => 'Core Error',
        \E_USER_ERROR    => 'User Error'
    );

    private function __construct()
    {
        //
    }

    public function getConfig()
    {
        return $this->config;
    }

    public static function init($api_key, &$config = null)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->api_key = $api_key;

            if (!$config) {
                $config = new Config();
            }

            self::$instance->config = $config;

            set_error_handler(array(self::$instance, 'errorHandler'));
            set_exception_handler(array(self::$instance, 'exceptionHandler'));
            register_shutdown_function(array(self::$instance, 'shutdownHandler'));
        } else {
            throw new Exception('The client has been already initialized');
        }

        return self::$instance;
    }

    public function test()
    {
        $return = $this->push('test');

        if ($return->success != 'true') {
            return false;
        } else {
            return true;
        }
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (array_key_exists($errno, self::$warnings) && !$this->getConfig()->getReportWarnings()) {
            return true;
        }

        $data = array(
            'errno'      => $errno,
            'errstr'     => $errstr,
            'errfile'    => $errfile,
            'errline'    => $errline,
            'errcontext' => $errcontext
        );

        $return = $this->push('reports', $data);

        return true;
    }

    public function exceptionHandler(Exception $exception)
    {
        if (!$this->getConfig()->getReportExceptions()) {
            return true;
        }

        $data = array(
            'errno'      => null,
            'exception'  => get_class($exception),
            'errstr'     => $exception->getMessage(),
            'errfile'    => $exception->getFile(),
            'errline'    => $exception->getLine(),
            'errcontext' => $exception->getTrace()
        );

        $return = $this->push('reports', $data);

        return true;
    }

    public function shutdownHandler()
    {
        if (!$error = error_get_last()) {
            return;
        }

        $data = array(
            'errno'      => \E_ERROR,
            'errstr'     => $error['message'],
            'errfile'    => $error['file'],
            'errline'    => $error['line'],
            'errcontext' => array()
        );

        $return = $this->push('reports', $data);

        return;
    }

    private function push($url, $data = null)
    {
        $ch = curl_init();
                
        curl_setopt($ch, CURLOPT_URL, self::$endpoint . $url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Api-Key: ' . $this->api_key));
        curl_setopt($ch, CURLOPT_PORT, 80);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConfig()->getTimeout());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            $data['language'] = 0;
            $data['environment'] = $this->getConfig()->getEnvironment();
            $data['bundle'] = $this->getConfig()->getBundle();
            $data['controller'] = $this->getConfig()->getController();
            $data['action'] = $this->getConfig()->getAction();
            $data['routing'] = $this->getConfig()->getRouting();
            $data['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $data['method'] = $_SERVER['REQUEST_METHOD'];
            $data['server_data'] = $_SERVER;
            $data['get_data'] = $_GET;
            $data['post_data'] = $_POST;
            $data['session_data'] = $_SESSION;

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $return = json_decode(curl_exec($ch));
        curl_close($ch);

        return $return;
    }
}
