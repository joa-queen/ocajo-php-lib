<?php

namespace Ocajo;

class Config
{
    protected $environment;
    protected $timeout = 2;
    protected $bundle = null;
    protected $controller = null;
    protected $action = null;
    protected $routing = null;
    protected $report_warnings = false;
    protected $report_exceptions = true;

    public function __construct($options)
    {
        $this->environment = (isset($options['environment']) ? $options['environment'] : 'default');

        if (isset($options['timeout']) && is_numeric($options['timeout'])) {
            $this->timeout = $options['timeout'];
        }

        if (isset($options['bundle'])) {
            $this->bundle = $options['bundle'];
        }

        if (isset($options['controller'])) {
            $this->controller = $options['controller'];
        }

        if (isset($options['action'])) {
            $this->action = $options['action'];
        }

        if (isset($options['routing'])) {
            $this->routing = $options['routing'];
        }

        if (isset($options['report_warnings'])) {
            $this->report_warnings = $options['report_warnings'];
        }
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getBundle()
    {
        return $this->bundle;
    }

    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getRouting()
    {
        return $this->routing;
    }

    public function setRouting($routing)
    {
        $this->routing = $routing;
    }

    public function getReportWarnings()
    {
        return $this->report_warnings;
    }

    public function setReportWarnings($report_warnings)
    {
        $this->report_warnings = $report_warnings;
    }

    public function getReportExceptions()
    {
        return $this->report_exceptions;
    }

    public function setReportExceptions($report_exceptions)
    {
        $this->report_exceptions = $report_exceptions;
    }
}
