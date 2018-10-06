<?php

namespace Fruit\Seeds;

use Fruit\HTTPError;
use Fruit\CompileKit\Renderable;
use Fruit\CompileKit\Value;
use Fruit\CheckKit\Repo;

class BodyParameter extends BaseSeed
{
    protected function configFilename(): string
    {
        return 'body_parameter.yml';
    }

    protected function validateConfig()
    {
        Repo::default()
            ->mustCheck($this->cfg, 'dict', ['elements' => [ '*' => [
                'type' => 'dict',
                'rules' => [ 'elements' => [
                    'import' => ['type' => 'string'],
                    'type' => ['type' => 'string', 'required' => true],
                    'rules' => ['type' => 'dict'],
                ]],
            ]]]);
    }

    private $cache = null;
    public function get()
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $this->cache = $this->validate();
        return $this->cache;
    }

    private function validate()
    {
        $req = $this->req('Fruit\Seeds\Request');
        $uri = $req->uri();
        $data = json_decode($req->body(), true);

        // check if uri is set
        if (! isset($this->cfg[$uri])) {
            return $data;
        }

        $rule = $this->cfg[$uri];
        $validator = $this->req('Fruit\Seeds\Validator');
        $r = [];
        if (isset($rule['rules'])) {
            $r = $rule['rules'];
        }

        $ret = $validator->check($data, $rule['type'], $r);
        if ($ret !== null) {
            throw new HTTPError(400, $ret);
        }

        return $data;
    }
}
