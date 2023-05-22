<?php

namespace Guideler\Tools\Classes;

use Guideler\Tools\Builders\Table\Builder as TableBuilder;
use Guideler\Tools\Builders\Table\Resource\Filters;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Guideler\Tools\Builders\Table\Resource\Grid;

class GuidelerController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private Grid $grid;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->grid($this->table()->grid());
            $this->filters($this->table()->filters());
            return $next($request);
        });
    }

    protected function table(): TableBuilder
    {
        return g_table(str(static::class)->replace('\\', '-')->lower()->slug()->toString());
    }

    protected function grid(Grid $grid): void
    {
    }

    protected function filters(Filters $filters): void
    {
    }
}
