<?php

namespace Guideler\Tools\Builders\Table\Resource;

use Illuminate\Support\Collection;

class Grid
{
    private array $columns = [];

    public function setColumns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function addColumn(string $label, string $prop, array $options = []): static
    {
        $this->columns[$prop] = (new Column($label, $prop, $options));
        return $this;
    }

    public function getColumn(string $name): Collection|Column
    {
        $column = $this->getColumnByProp($name);

        if (!$column)
            $column = $this->getColumnByName($name);

        if ($column)
            return $this->columns[$column['prop']];

        return collect();
    }

    public function getColumns($type = null): array|string|Collection
    {
        return $this->get($type);
    }

    public function removeColumn(string $name): static
    {
        $column = $this->getColumn($name);
        if ($column)
            unset($this->columns[$column['prop']]);

        return $this;
    }

    public function removeColumns(array $names): static
    {
        foreach ($names as $name)
            $this->removeColumn($name);

        return $this;
    }

    public function resetColumns(): static
    {
        $this->columns = [];
        return $this;
    }

    public function setColumnOption(string $name, string $option_name, mixed $value ): static
    {
        $column = $this->getColumn($name);

        if ($column instanceof Column)
            $column = $column->toCollection();

        if (!count($column))
            return $this;

        $column = $column->dot()->toArray();
        $column['options.'.$option_name] = $value;

        $options = collect($column)->undot();
        $this->columns[$column['prop']] =  (new Column($column['label'], $column['prop'], $options['options']));
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
        return collect($this->columns)->map(function (Column $item) {
            return $item->toCollection();
        });
    }

    private function get($type = null): array|string|Collection
    {
        return match ($type) {
            'array' => $this->toArray(),
            'json' => $this->toJson(),
            'collection' => $this->toCollection(),
            default => $this->columns,
        };
    }

    private function getColumnByProp(string $prop): ?Collection
    {
        return collect($this->toCollection())->where('prop', $prop)->first();
    }

    private function getColumnByName(string $name): ?Collection
    {
        return collect($this->toCollection())->where('label', $name)->first();
    }
}
