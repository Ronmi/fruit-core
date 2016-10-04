<?php

namespace Fruit;

class Injector
{
    private $data;
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    public function setData($key, $value)
    {
        if ($value !== null) {
            $this->data[$key] = $value;
        }
    }

    public function inject($url, AbstractHandler $obj, $method)
    {
        foreach ($this->data as $k => $v) {
            $obj->context($k, $v);
        }
    }

    public function setup(\Fruit\RouteKit\Mux $mux)
    {
        $mux->setIntercaptor(array($this, 'inject'));
    }
}
