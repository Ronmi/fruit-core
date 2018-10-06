<?php

namespace Fruit;

abstract class Controller
{
    private $modules;

    final public function inject(Svalbard $mgr)
    {
        $this->modules = $mgr;
    }

    public function __get(string $name)
    {
        $seed = $this->modules->get($name);
        if ($seed === null) {
            return null;
        }

        return $seed->get();
    }

    protected function module(string $name): Seed
    {
        return $this->modules->get($name);
    }
}
