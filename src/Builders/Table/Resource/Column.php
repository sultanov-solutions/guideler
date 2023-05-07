<?php

namespace Guideler\Tools\Builders\Table\Resource;

use Guideler\Tools\Builders\Resource;
use Illuminate\Support\Collection;

class Column extends Resource
{
    public function getLabel()
    {
        return $this->resource->get('label', $this->resource->get('prop', null));
    }

    public function getProp()
    {
        return $this->get('prop');
    }

    public function getOptions()
    {
        return $this->get('options');
    }

    public function getOption($key)
    {
        $options = $this->get('options');
        if (isset($options[$key]))
            return $options[$key];

        return null;
    }

    protected function collectResource($resource): ?Collection
    {
        if (!count($resource))
            abort(500, 'Column resource is empty');

        if (count($resource) === 1)
        {
            return collect($resource[0]);
        }
        else
        {
            $params = [
                'label' => $resource[0] ?? null,
                'prop' => $resource[1] ?? null,
                'options' => $resource[2] ?? [],
            ];

            if (!isset($params['label']))
                abort(500, 'Column label is empty');

            if (!isset($params['prop']))
                abort(500, 'Column prop is empty');

            return collect($params);
        }
    }

    public function updateOption($key, $value)
    {
        $options = $this->get('options');
        dd($options);
    }
}
