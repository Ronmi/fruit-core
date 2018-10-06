<?php

namespace Fruit\Seeds;

use Fruit\Seed;
use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\Value;
use Fruit\CompileKit\Block;

/**
 * Environment returns current running environment.
 *
 * This seed always returns one of "dev" and "prod": "dev" is for developing and
 * testing, "prod" is for production server.
 *
 * As the core spirit of Fruit framework: work/test with raw code, build/compile in
 * CI pipeline, deploy compiled code to production. This seed returns "prod" in
 * compiled version, and "dev" otherwise.
 */
class Environment extends Seed
{
    protected $env;
    protected function init()
    {
        $this->env = 'dev';
    }

    public function get()
    {
        return $this->env;
    }

    public function compile(): Renderable
    {
        list($ret, $init) = Seed::genCode();
        $ret->extends('\Fruit\Seeds\Environment');
        $init->append((new Block)->assign(
            Value::as('$this->env'),
            Value::of('prod')
        ));

        return $ret;
    }
}
