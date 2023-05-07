<?php

namespace Guideler\Tools\Builders\Options;

use Illuminate\Support\Collection;

class Options
{
    private array $options = [];

    public function setOptions(array $options): static
    {
        foreach ($options as $key => $value)
            $this->addOption($key, $value);

        return $this;
    }

    public function addOption(string $key, mixed $value): static
    {
        $this->options[] = (new Option(['key' => $key, 'value' => $value]));
        return $this;
    }

    public function getOption(string $key): Collection|Option
    {
        $option = collect($this->toCollection())->search(fn($item) => $item['key'] === $key);
        if ($option !== false)
            return $this->options[$option];

        return collect();
    }

    public function getOptions($type = null): array|string|Collection
    {
        return $this->get($type);
    }

    public function removeOption(string $key): static
    {
        $option = $this->getOption($key);
        if ($option)
            $this->options = collect($this->options)->filter(fn($item) => $item['key'] !== $key)->values()->toArray();

        return $this;
    }

    public function removeOptions(array $keys): static
    {
        foreach ($keys as $key)
            $this->removeOption($key);

        return $this;
    }

    public function resetOptions(): static
    {
        $this->options = [];
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
        return collect($this->options)->map(function (Option $item) {
            return $item->toCollection();
        });
    }

    private function get($type = null): array|string|Collection
    {
        return match ($type) {
            'json' => $this->toJson(),
            'collection' => $this->toCollection(),
            default => $this->toArray(),
        };
    }
}
