<?php

namespace Fruit\Seeds;

use Fruit\Seed;
use Fruit\CheckKit\Repo;
use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\Value;
use Fruit\CompileKit\Block;

class Validator extends Seed
{
    private $repo = null;
    protected function init()
    {
        $this->repo = new Repo;
        $cfg = $this->readConfig('validators.yml');
        Repo::default()->check($this->cfg, 'dict', ['elements' => [
            '*' => [
                'type' => 'string'
            ],
        ]]);

        foreach ($cfg as $alias => $cls) {
            $this->repo->register($alias, $cls);
        }
    }

    public function get()
    {
        return $this->repo;
    }

    public function compile(): Renderable
    {
        list($ret, $init) = self::genCode();
        $init->append((new Block)->assign(
            Value::as('$this->repo'),
            $this->repo->compile()
        ));

        return $ret;
    }
}
