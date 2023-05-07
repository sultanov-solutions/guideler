<?php

namespace Guideler\Tools\Builders;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Resource
{
    protected ?Collection $resource;

    public function __construct()
    {
        $this->resource = $this->collectResource(func_get_args());
    }

    public function toArray(): array
    {
        return $this->resource->toArray();
    }

    public function toJson(): string
    {
        return $this->resource->toJson();
    }

    public function toCollection(): Collection
    {
        return $this->resource->collect();
    }

    protected function get($key, $default = null)
    {
        return $this->resource->get($key, $default);
    }

    protected function collectResource($resource): ?Collection
    {
        return collect($resource[0]);
    }

    public function update($key, $value): static
    {
        $resource = Arr::dot($this->resource->toArray());
        $resource[$key] = $value;
        $resource = Arr::undot($resource);
        $this->updateResource($resource);
        return $this;
    }

    protected function updateResource(Collection|array $resource): static
    {
        $this->resource = collect($resource);
        return $this;
    }
}
