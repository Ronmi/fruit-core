<?php

namespace Fruit\Utils;

use Fruit\Request;

class NativeRequest implements Request
{
    private $data;
    private $headers;

    public function __construct()
    {
        $this->data = file_get_contents('php://input');
        $this->headers = $this->getHeaders();
    }

    private function getHeaders()
    {
        $ret = array();
        foreach ($_SERVER as $k => $v) {
            if (substr($k, 0, 5) <> 'HTTP_') {
                continue;
            }
            $h = str_replace(
                ' ',
                '-',
                ucwords(str_replace('_', ' ', strtolower(substr($k, 5))))
            );
            $ret[$h] = $v;
        }
        return $ret;
    }

    public function body(): string
    {
        return $this->data;
    }

    public function get(): array
    {
        return $_GET;
    }

    public function post(): array
    {
        return $_POST;
    }

    public function server(): array
    {
        return $_SERVER;
    }

    public function env(): array
    {
        return $_ENV;
    }

    public function form(): array
    {
        return $_POST + $_GET;
    }

    public function header(): array
    {
        return $this->headers;
    }

    public function cookie(): array
    {
        return $_COOKIE;
    }

    public function file(): array
    {
        return $_FILES;
    }

    public function uri(): string
    {
        $path = $_SERVER['PATH_INFO'];
        if (!$path) {
            $path = $_SERVER['REQUEST_URI'];
        }
        return $path;
    }

    public function method():string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
