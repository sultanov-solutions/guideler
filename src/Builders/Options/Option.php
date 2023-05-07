<?php

namespace Guideler\Tools\Builders\Options;

use Guideler\Tools\Builders\Resource;

class Option extends Resource
{
    public function getKey()
    {
        return $this->resource->get('key');
    }

    public function getValue()
    {
        return $this->get('value');
    }
}
