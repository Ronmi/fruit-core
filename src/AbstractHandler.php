<?php

namespace Fruit;

abstract class AbstractHandler
{
    protected $internalContext; // an array holding key-value pairs shared within this handler class
    protected $request; // an Request instance, guarantee to exist

    public function context(string $key, $value = null)
    {
        if (!is_array($this->internalContext)) {
            $this->internalContext[$key];
        }
        if ($value !== null) {
            $ret = $this->internalContext[$key];
            $this->internalContext[$key] = $value;
            if ($key === 'request') {
                $this->request = $value;
            }
            return $ret;
        }

        if (!isset($this->internalContext[$key])) {
            return null;
        }
        return $this->internalContext[$key];
    }
}
