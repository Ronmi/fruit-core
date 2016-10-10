<?php

namespace Fruit;

class FPMRequest extends Request
{
    public function parse()
    {
        $this->form = array_merge($_GET, $_POST);
        $this->file = $_FILE;
        $this->env = $_SERVER;
    }

    private $bodyCache;
    public function body()
    {
        if ($this->bodyCache === null) {
            $this->bodyCache = file_get_contents('php://input');
        }

        return $this->bodyCache;
    }
}
