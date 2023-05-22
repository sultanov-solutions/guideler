<?php

use Guideler\Tools\Builders\Table\Builder;
use Guideler\Tools\Builders\Table\Resource\Filters;
use Guideler\Tools\Builders\Table\Resource\Grid;
use Guideler\Tools\Builders\Table\Options;

if (!function_exists('g_table'))
{
    function g_table($instance = 'default'): Builder
    {
        return Builder::getInstance($instance);
    }
}

if (!function_exists('g_grid'))
{
    function g_grid($instance = 'default'): Grid
    {
        return g_table($instance)->grid();
    }
}

if (!function_exists('g_filters'))
{
    function g_filters($instance = 'default'): Filters
    {
        return g_table($instance)->filters();
    }
}
