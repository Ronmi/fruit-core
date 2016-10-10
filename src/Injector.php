<?php

namespace Fruit;

use Fruit\RouteKit\Interceptor;

class Injector implements Interceptor
{
    private $data;
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    public function setData(string $key, $value)
    {
        if ($value !== null) {
            $this->data[$key] = $value;
        }
    }

    public function intercept(string $url, $obj, string $method)
    {
        if (!($obj instanceof AbstractHandler)) {
            return;
        }

        foreach ($this->data as $k => $v) {
            $obj->context($k, $v);
        }
    }

    public function setup(\Fruit\RouteKit\Mux $mux)
    {
        $mux->setInterceptor($this);
    }

    public static function __set_state(array $data)
    {
        return new self($data['data']);
    }
}
