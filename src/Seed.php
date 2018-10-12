<?php

namespace Fruit;

use Fruit\CompileKit\Compilable;
use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\UserMethod;
use Fruit\CompileKit\AnonymousClass;
use Fruit\PathKit\Path;
use Symfony\Component\Yaml\Yaml;

/**
 * Seed is the module system of Fruit framework.
 *
 * Unlike modules in other framework, Seed is actually a Compilable which reads
 * configurations to produce value. Implementation could
 *
 * 1. Read configurations at runtime,
 * 2. Or embed configuration in generated code, which is suggested.
 */
abstract class Seed implements Compilable
{
    /**
     * Get the value.
     *
     * Implementations shoud apply their own cache mechanism here.
     */
    abstract public function get();

    /**
     * lifecycle hook, called in constructor, after $this->basePath is set, but
     * before $this->req() is ready. You should read and parse config here.
     */
    protected function init()
    {
    }

    /**
     * Retrieve list of dependencies.
     *
     * You SHOULD return full qualified classname in array, like
     * `['Fruit\Seeds\Validator']`.
     */
    protected function depends(): array
    {
        return [];
    }

    /**
     * helper to read yaml file.
     */
    final protected function readConfig(string $fn)
    {
        $realFN = (new Path('config', $this->basePath))->join($fn)->normalize();
        return Yaml::parseFile(
            $realFN,
            Yaml::PARSE_DATETIME | Yaml::PARSE_CONSTANT
        );
    }

    private $svalbard;
    protected $basePath;
    final public function __construct(Svalbard $storage)
    {
        $this->basePath = $storage->getBasePath();
        $this->init();
        $this->svalbard = $storage;

        foreach ($this->depends() as $cls) {
            $this->svalbard->byClass($cls);
        }
    }

    final protected function req(string $className)
    {
        $ret = $this->svalbard->byClass($className);
        if ($ret !== null) {
            $ret = $ret->get();
        }

        return $ret;
    }

    /**
     * helper
     *
     * @return [AnonymousClass, UserMethod(init)]
     */
    final public static function genCode(): array
    {
        $c = (new AnonymousClass)->extends("\\" . get_called_class());
        $init = $c->can('init');

        return [$c, $init];
    }

    /**
     * Implementing Compilable interface.
     *
     * It is slightly more specific compares to other Compilables:
     *
     * 1. You MUST returns an AnonymousClass instance which implements Seed.
     * 2. You MUST NOT set constructor arguments. Svalbard will do it.
     *
     * The default implementation just return an anonymous class which
     * extends the called class.
     */
    public function compile(): Renderable
    {
        return (new AnonymousClass)->extends("\\" . get_called_class());
    }
}
