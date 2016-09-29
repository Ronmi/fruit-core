<?php

namespace Fruit;

class Input
{
    public static function parse($data = '')
    {
        if ($data === '') {
            $data = file_get_contents('php://input');
        }

        return json_decode($data, true);
    }
}
