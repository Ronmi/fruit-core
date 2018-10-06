<?php

namespace Fruit;

interface Request
{
    public function body(): string;
    public function get(): array;
    public function post(): array;
    public function server(): array;
    public function env(): array;
    public function form(): array;
    public function header(): array;
    public function cookie(): array;
    public function file(): array;
    public function uri(): string;
    public function method(): string;
}
