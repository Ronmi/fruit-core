<?php

namespace Fruit\Seeds;

use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\AnonymousClass;
use Fruit\CheckKit\Repo;
use Fruit\Utils\NativeRequest;

class Request extends BaseSeed
{
    private $req = null;

    protected function configFilename(): string
    {
        return 'request.yml';
    }

    private static $validTypes = ['native'];

    protected function validateConfig()
    {
        Repo::default()->mustCheck($this->cfg, 'dict', [
            'strict' => true,
            'elements' => [
                'type' => [
                    'type' => 'string',
                    'rules' => [
                        'regex' => 'native$', // currently supports native only
                        'regex_mode' => 'i',
                    ],
                ],
            ],
        ]);
        $this->cfg['type'] = strtolower($this->cfg['type']);
    }

    private $readed = false;
    private function read()
    {
        switch ($this->cfg['type']) {
            case 'native':
                $this->req = new NativeRequest;
        }

        $this->readed = true;
    }

    public function get()
    {
        if (!$this->readed) {
            $this->read();
        }

        return $this->req;
    }
}
