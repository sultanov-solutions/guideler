<?php

namespace Guideler\Tools\Builders\Table\Resource;

use Guideler\Tools\Builders\Resource;
use Illuminate\Support\Collection;

class Filter extends Resource
{
    protected array $conditions = [
        'eq',  // equal
        'bw',  // between
        'lt',  // less than
        'lte', // less than or equal
        'gt',  // greater than
        'gte', // greater than or equal
        'ft',  // from to
        'sw',  // switch true/false
        'q',  // query string
    ];

    protected array $filterView = [
        'text',
        'number',
        'date',
        'dateTime',
        'color',
        'checkbox',
        'select',
        'multiSelect',
        'color',
        'textarea',
    ];

    public function getLabel()
    {
        return $this->resource->get('label', $this->resource->get('prop'));
    }

    public function getProp()
    {
        return $this->get('prop');
    }

    public function getCondition()
    {
        return $this->get('condition');
    }

    public function getType()
    {
        return $this->get('type');
    }

    public function getOptions()
    {
        return $this->get('options');
    }

    public function getOption($key)
    {
        if ($option = $this->get($key))
            return $option;

        return null;
    }

    protected function collectResource($resource): ?Collection
    {
        return $this->hydrateFilter($resource);
    }

    private function hydrateFilter(array $filter): Collection
    {
        $newFilter = [
            'label' => $filter[0],
            'prop' => $filter[1],
        ];

        $options = $filter[2] ?? [];


        $newFilter['condition'] = $options['condition'] ?? 'eq';
        if (!isset($options['condition']) || !in_array($options['condition'], $this->conditions))
            $newFilter['condition'] = 'eq';

        $newFilter['type'] = $options['type'] ?? 'text';
        if (!isset($options['type']) || !in_array($options['type'], $this->filterView))
            $newFilter['type'] = 'text';


        $newFilter = array_merge($options, $newFilter);

        return collect($newFilter);
    }
}
