<?php

namespace Fruit;

use Fruit\RouteKit\Router;
use Fruit\RouteKit\Mux;
use Fruit\RouteKit\Interceptor;
use Fruit\CompileKit\AnonymousClass;
use Fruit\CompileKit\Block;
use Fruit\CompileKit\Value;

class Core
{
    private $router;
    private $module;
    private $running = false;

    protected function __construct(Router $router, Svalbard $module)
    {
        $this->router = $router;
        $this->module = $module;
    }

    public static function create(string $baseDir): self
    {
        $mux = new Mux;
        $mgr = new Svalbard($baseDir);

        $mux->setInterceptor(new Injector);
        $mux->getInterceptor()->set($mgr);
        return new self($mux, $mgr);
    }

    private function bootup(string $method, string $path)
    {
        $this->running = true;
        $err = null;
        $ret = null;

        try {
            $ret = $this->router->dispatch($method, $path);
        } catch (HTTPStatus $e) {
            http_response_code($e->getCode());
            list($ret, $err) = $e->render();
        } catch (\Exception $e) {
            http_response_code(500);
            $err = $e->getMessage();
        }

        if ($ret instanceof RawResult) {
            return;
        }

        $output = [];

        if ($err !== null) {
            $output['error'] = $err;
        } else {
            $output['data'] = $ret;
        }

        header('Content-Type: application/json');
        echo json_encode($output);
    }

    public function boot()
    {
        $req = $this->module->byClass('Fruit\Seeds\Request')->get();
        $this->bootup($req->method(), $req->uri());
    }

    public function genProd(): string
    {
        $this->atRuntime();

        $module = $this->module->compile();
        $module->rawArgs('__DIR__');
        $c = (new AnonymousClass)
            ->extends('\Fruit\Core')
            ->setArgs($this->router->compile(), $module);

        // override constructor
        $c
            ->can('__construct')
            ->rawArg('r')
            ->rawArg('x')
            ->line('parent::__construct($r, $x);')
            ->line('$r->getInterceptor()->set($x);');

        $file = (new Block)
            ->asFile()
            ->req('vendor/autoload.php')
            ->space()
            ->stmt(
                Value::as('('),
                $c,
                Value::as(')->boot()')
            );

        return $file->render(true);
    }

    public function __get(string $name)
    {
        $this->atRuntime();

        if ($name === 'router') {
            return $this->router;
        }
        if ($name === 'module') {
            return $this->module;
        }
    }

    private function atRuntime()
    {
        if (!$this->running) {
            return;
        }
        throw new \Exception('Core facilities are disabled at runtime.');
    }
}
