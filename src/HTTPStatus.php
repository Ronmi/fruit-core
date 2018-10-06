<?php

namespace Fruit;

class HTTPStatus extends \Exception
{
    public $data;

    public function __construct(int $code, $data = null)
    {
        if ($data instanceof \Throwable) {
            parent::__construct('HTTP Error', $code, $data);
            $this->data = $data->getMessage();
            return;
        }

        parent::__construct('HTTP Error', $code);
        $this->data = $data;
    }

    public function render(): array
    {
        return [$this->data, null];
    }
}
