<?php

namespace FruitTest;

class ModuleTest extends \PHPUnit\Framework\TestCase
{
    public function testCompile()
    {
        $mod = new \Fruit\ModuleManager();
        $mod->register('fake', new FakeModule);
        $actual = $mod->compile();

        eval('$m = ' . $actual . ';');
        $this->assertInstanceOf('Fruit\ModuleManager', $m);
        $this->assertEquals(1, $m->get('fake'));
    }
}
