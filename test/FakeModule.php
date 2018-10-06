<?php

namespace FruitTest;

class FakeModule implements \Fruit\Module
{
    public function create()
    {
        return 1;
    }

    public function compile(): string
    {
        return 'new \FruitTest\FakeModule';
    }
}
