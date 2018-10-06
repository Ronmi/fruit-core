<?php

namespace Fruit\Seeds;

use Fruit\CompileKit\Renderable;
use Fruit\CheckKit\Repo;
use PDO;

class SimplePDO extends BaseSeed
{
    protected function configFilename(): string
    {
        return 'simple_pdo.yml';
    }

    protected function validateConfig()
    {
        $repo = Repo::default();
        $repo->mustCheck($this->cfg, 'dict', [
            'strict' => true,
            'elements' => [
                'dsn' => ['type' => 'string', 'required' => true],
                'user' => ['type' => 'string', 'required' => true],
                'pass' => ['type' => 'string', 'required' => true],
                'options' => ['type' => 'dict']
            ],
        ]);
    }

    private $pdo = null;
    public function get()
    {
        if ($this->pdo === null) {
            $this->createPDO();
        }

        return $this->pdo;
    }

    private function createPDO()
    {
        if (isset($this->cfg['options'])) {
            $this->pdo = new PDO(
                $this->cfg['dsn'],
                $this->cfg['user'],
                $this->cfg['pass'],
                $this->cfg['options']
            );
            return;
        }

        $this->pdo = new PDO(
            $this->cfg['dsn'],
            $this->cfg['user'],
            $this->cfg['pass']
        );
    }
}
