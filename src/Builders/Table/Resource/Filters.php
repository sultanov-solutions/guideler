<?php

namespace Guideler\Tools\Builders\Table\Resource;

use Illuminate\Support\Collection;

class Filters
{
    private array $filters = [];

    public function __construct()
    {
        if (!isset($this->filters['id']))
            $this->addFilter('ID', 'id', ['type' => 'number', 'filter' => 'eq']);
    }

    public function disableIDFilter(): static
    {
        unset($this->filters['id']);

        return $this;
    }

    public function setFilters(array $filters): static
    {
        foreach ($filters as $filter)
            $this->addFilter($filter['label'], $filter['prop'], $filter);

        return $this;
    }

    public function addFilter(string $label, string $prop, array $options = []): static
    {
        $condition = $options['condition'] ?? 'eq';

        switch ($condition) {
            case 'bw':
                $this->filters[$prop . '_from'] = (new Filter($label, $prop . '_from', $options));
                $this->filters[$prop . '_to'] = (new Filter($label, $prop . '_to', $options));
                break;
            default:
                $this->filters[$prop] = (new Filter($label, $prop, $options));
        }

        return $this;
    }

    public function getFilter(string $key): Collection|Filter
    {
        $filter = $this->getFilterByProp($key);

        if (!$filter)
            $filter = $this->getFilterByName($key);

        if ($filter)
            return $this->filters[$filter['prop']];

        return collect();
    }

    public function getFilters($type = null): array|string|Collection
    {
        return $this->get($type);
    }

    public function removeFilter(string $key): static
    {
        $filter = $this->getFilter($key);
        if ($filter)
            unset($this->filters[$filter['prop']]);

        return $this;
    }

    public function removeFilters(array $names): static
    {
        foreach ($names as $name)
            $this->removeFilter($name);

        return $this;
    }

    public function resetFilters(): static
    {
        $this->filters = [];
        return $this;
    }

    public function setFilterOption(string $name, string $option_name, mixed $value ): static
    {
        $filter = $this->getFilter($name);

        if ($filter instanceof Filter)
            $filter = $filter->toCollection();

        if (!count($filter))
        {
            $bw_filters = $this->issetBWFilter($name);

            if (count($bw_filters))
            {
                foreach ($bw_filters as $bw_filter){
                    $this->setFilterOption($bw_filter->getProp(), $option_name, $value);
                }

                return $this;
            }
        }

        if (!count($filter))
            return $this;

        $filter = $filter->dot()->toArray();
        $filter[$option_name] = $value;

        $this->filters[$filter['prop']] = (new Filter($filter['label'], $filter['prop'], collect($filter)->undot()->toArray()));
        return $this;
    }

    public function toArray(string $only = null): array
    {
        return $this->toCollection()->toArray();
    }

    public function toJson(): string
    {
        return $this->toCollection()->toJson();
    }

    public function toCollection(): Collection
    {
        return collect($this->filters)->map(function (Filter $item) {
            return $item->toCollection();
        });
    }

    private function issetBWFilter($name)
    {
        $filters = [];
        $filter_from = $this->getFilter($name . '_from');
        $filter_to = $this->getFilter($name . '_to');

        if ($filter_from)
            $filters[] = $filter_from;

        if ($filter_to)
            $filters[] = $filter_to;

        return $filters;
    }

    private function get($type = null): array|string|Collection
    {
        return match ($type) {
            'array' => $this->toArray(),
            'json' => $this->toJson(),
            'collection' => $this->toCollection(),
            default => $this->filters,
        };
    }

    private function getFilterByProp(string $key): ?Collection
    {
        return collect($this->toCollection()->values())->where('prop', $key)->first();
    }

    private function getFilterByName(string $name): ?Collection
    {
        return collect($this->toCollection())->where('label', $name)->first();
    }
}
