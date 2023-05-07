<?php

namespace Guideler\Tools\Builders\Table;

use Guideler\Tools\Builders\Options\Options;
use Guideler\Tools\Builders\Table\Resource\Filters;
use Guideler\Tools\Builders\Table\Resource\Grid;
use Exception;

class Builder
{
    private static array $instances = [];

    private Grid $grid;

    private Filters $filters;

    private Options $options;

    private function __construct()
    {
        $this->grid = app(Grid::class);
        $this->filters = app(Filters::class);
        $this->options = app(Options::class);
    }

    protected function __clone() {}

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance($instance)
    {
        $subclass = $instance ?? static::class;

        if (!isset(self::$instances[$subclass]))
            self::$instances[$subclass] = new static();

        return self::$instances[$subclass];
    }

    public function grid(): Grid
    {
        return $this->grid;
    }

    public function filters(): Filters
    {
        return $this->filters;
    }

    public function options(): Options
    {
        return $this->options;
    }

    public function get($type = null)
    {
        return [
            'columns' => $this->grid->getColumns('collection'),
            'filters' => $this->filters->getFilters('collection'),
            'options' => $this->options->getOptions('collection')->values()->toArray(),
        ];
    }
}
