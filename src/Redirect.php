<?php

namespace Fruit;

class Redirect extends HTTPStatus
{
    public function __construct(string $dest, int $code = 302)
    {
        parent::__construct($code, $dest);
    }

    public function render(): array
    {
        header('Location: ' . $this->data);
        exit(0);
    }
}
