<?php

namespace Fruit;

abstract class Handler
{
    protected $internalContext; // an array holding key-value pairs shared within this handler class

    public function context($key, $value = null)
    {
        if (!is_array($this->internalContext)) {
            $this->internalContext[$key];
        }
        if ($value !== null) {
            $this->internalContext[$key] = $value;
        }

        if (!isset($this->internalContext[$key])) {
            return null;
        }
        return $this->internalContext[$key];
    }
}
