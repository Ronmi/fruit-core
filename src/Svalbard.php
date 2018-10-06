<?php

namespace Fruit;

use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\AnonymousClass as C;
use Fruit\CompileKit\UserArray as Arr;
use Fruit\CompileKit\Block;
use Fruit\CompileKit\Value;
use ReflectionClass;

class Svalbard
{
    protected $basePath;
    protected $mods = [];
    protected $mapping = [];

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function register(string $name, string $className): self
    {
        $ref = new ReflectionClass($className);
        if (! $ref->isSubclassOf('\Fruit\Seed')) {
            throw new \Exception($className . ' is not a valid seed');
        }

        $this->mapping[$name] = $className;

        if (! isset($this->mods[$className])) {
            $this->mods[$className] = new $className($this);
        }
        return $this;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function get(string $name)
    {
        if (! isset($this->mapping[$name])) {
            throw new \Exception($name . ' is not registered in storage.');
        }

        return $this->mods[$this->mapping[$name]];
    }

    public function byClass(string $className)
    {
        if (! isset($this->mods[$className])) {
            $this->mods[$className] = new $className($this);
        }

        return $this->mods[$className];
    }

    /**
     * This method implements Compilable but with slightly different detail.
     *
     * The compiled Renderable is always an AnonymousClass, which accepts exactly
     * one constructor argument which denotes base path of your project, but value
     * is not specified. The Fruit\Core will initialize this for you.
     *
     * This information is here in case you need to do some *black magic*.
     *
     * WARNING: As some seeds might depends on other seeds, this method will call
     * `byClass` to each registered seeds several times, ensuring all dependencies
     * are registered before generating code.
     *
     * @see Fruit\CompileKit\Compilable
     */
    public function compile(): Renderable
    {
        // test dependencies
        $cnt = 0;
        while (($l = count($this->mods)) !== $cnt) {
            $cnt = $l;
            foreach ($this->mods as $k => $v) {
                $this->byClass($k);
            }
        }

        $mods = [];
        $fac = [];
        $cnt = 0;
        foreach (array_keys($this->mods) as $k) {
            $key = '_f' . $cnt++;
            $body = (new Block)->return(
                $this->mods[$k]->compile()->rawArgs('$this')
            );

            $fac[$key] = $body;
            $mods[$k] = $key;
        }

        $c = (new C)->extends('\Fruit\Svalbard');

        foreach ($fac as $name => $body) {
            $c->can($name, 'private')->append($body);
        }

        $body = (new Block)
            ->line('parent::__construct($path);')
            ->line('$this->cache = [];')
            ->assign(
                Value::as('$this->mods'),
                new Arr($mods)
            )
            ->assign(
                Value::as('$this->mapping'),
                new Arr($this->mapping)
            );
        $c->has('cache', 'private');
        $c->can('__construct')
            ->append($body)
            ->accept('$path', 'string');

        // override byClass, disable isset() checking
        $body = (new Block)
            ->line('if (isset($this->cache[$className])) {')
            ->child((new Block)->return(Value::as('$this->cache[$className]')))
            ->line('}')
            ->space()
            ->line('$f = $this->mods[$className];')
            ->line('$ret = $this->cache[$className] = self::$f();')
            ->line('return $ret;');
        $c->can('byClass')->append($body)->accept('$className')->type('string');

        // override get, disable isset() checking
        $body = (new Block)
            ->return(Value::as('$this->byClass($this->mapping[$name])'));
        $c->can('get')->append($body)->accept('$name')->type('string');

        // disable register in runtime
        $c->can('register')->rawArg('$n', 'string')->rawArg('$c', 'string');

        return $c;
    }
}
