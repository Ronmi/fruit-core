<?php

namespace Fruit;

use Fruit\RouteKit\Router;

class Bootstrap
{
    public static function boot(Router $router, $method, $path)
    {
        $err = '';
        ob_start();
        try {
            $ret = $router->dispatch($method, $path);
        } catch (HTTPStatus $h) {
            http_response_code($h->getCode());
            $err = $h->getMessage();
        } catch (\Exception $e) {
            http_response_code(500);
            $err = $e->__toString();
        }
        ob_end_flush();

        if ($err !== '') {
            $ret = $err;
        }

        echo json_encode($ret);
    }

    public static function FromFPM(Router $router)
    {
        $path = $_SERVER['PATH_INFO'];
        if (!$path) {
            $path = $_SERVER['REQUEST_URI'];
        }
        self::boot($router, $_SERVER['REQUEST_METHOD'], $path);
    }
}
