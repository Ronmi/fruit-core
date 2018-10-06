<?php

namespace Fruit;

class HTTPError extends HTTPStatus
{
    public function render(): array
    {
        return [null, $this->data];
    }
}
