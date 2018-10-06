<?php

namespace Fruit;

use Fruit\RouteKit\Interceptor;
use Fruit\CompileKit\Compilable;
use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\Value;

class Injector implements Interceptor, Compilable
{
    private $m;
    public function set(Svalbard $m)
    {
        $this->m = $m;
    }

    public function intercept(string $url, $obj, string $method)
    {
        if ($obj instanceof Controller) {
            $obj->inject($this->m);
        }
    }

    public function compile(): Renderable
    {
        return Value::as('new \Fruit\Injector');
    }
}
