<?php

namespace Guideler\Tools\Classes;

use Guideler\Tools\Builders\Table\Builder as TableBuilder;
use Guideler\Tools\Builders\Table\Resource\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    public function applyFilters(Builder $model)
    {
        $filters = $this->requestFilters();
        if (count($filters))
        {
            $tableFilters = $this->table()->filters()->toArray();
            foreach ($filters as $filter_key => $val)
            {
                if (empty($val))
                    continue;

                if (isset($tableFilters[$filter_key]))
                {
                    $condition = $tableFilters[$filter_key]['condition'] ?? 'eq';
                    match ($condition) {
                        'eq', 'sw' => $model->where($filter_key, $val),
                        'lt' => $model->where($filter_key, '<', $val),
                        'lte' => $model->where($filter_key, '<=', $val),
                        'gt' => $model->where($filter_key, '>', $val),
                        'gte' => $model->where($filter_key, '>=', $val),
                        'q' => $model->where($filter_key, 'ilike', "%{$val}%"),
                    };
                }
            }
        }
        
        return $model;
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

    private function requestFilters(): array
    {
        $acceptedFilters = collect($this->table()->filters()->toArray())->keys()->toArray();
        $filters = request()->get('filter', []);
        $filters = collect($filters)->filter(function ($filter, $key) use ($acceptedFilters){
            return in_array($key, $acceptedFilters);
        });

        return $filters->toArray();
    }

}
