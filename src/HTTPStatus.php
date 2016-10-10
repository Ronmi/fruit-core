<?php

namespace Fruit;

class HTTPStatus extends \Exception
{
    public function __construct(int $code, string $msg = '')
    {
        parent::__construct('HTTP Status ' . $code, $code);
        if ($msg !== '') {
            $this->message = $msg;
        }
    }
}
