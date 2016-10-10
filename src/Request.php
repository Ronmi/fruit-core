<?php

namespace Fruit;

abstract class Request
{
    public $form = array(); // roughly $_REQUEST, not guarantee to exist before calling parse()
    public $file = array(); // same as $_FILE, not guarantee to exist before calling parse()
    public $env = array(); // yes, $_SERVER, not guarantee to exist before calling parse()

    /**
     * This function prepares form, file and env.
     */
    abstract public function parse();

    /**
     * This method returns request body, not including multipart data.
     */
    abstract public function body(): string;

    private $jsonCached;
    private $json;
    public function json()
    {
        if (!$this->jsonCached) {
            $this->json = json_decode($this->body(), true);
            $this->jsonCached = true;
        }

        return $this->json;
    }
}
