<?php

namespace Fruit\Seeds;

use Fruit\Seed;
use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\Value;
use Fruit\CompileKit\Block;

abstract class BaseSeed extends Seed
{
    protected $cfg;

    protected function configFilename(): string
    {
        return '';
    }

    protected function validateConfig()
    {
    }

    protected function init()
    {
        $fn = $this->configFilename();
        if ($fn === '') {
            return;
        }

        $this->cfg = $this->readConfig($fn);
        $this->validateConfig();
    }

    public function compile(): Renderable
    {
        list($ret, $init) = self::genCode();
        $init->append((new Block)->assign(
            Value::as('$this->cfg'),
            Value::of($this->cfg)
        ));

        return $ret->extends("\\" . get_called_class());
    }
}
