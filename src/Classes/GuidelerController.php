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

    public array $acceptedFilters = [];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->grid($this->table()->grid());
            $this->filters($this->table()->filters());
            $this->acceptedFilters = collect($this->table()->filters()->toArray())->keys()->toArray();
            return $next($request);
        });
    }

    public function applyFilters(Builder $model)
    {
        $filters = $this->requestFilters();
        $tableFilters = $this->table()->filters()->toArray();

        if (count($filters))
        {
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

        if (request()->has('s') && !empty(request()->get('s')))
        {
            foreach ($this->acceptedFilters as $filter)
            {
                $condition = $tableFilters[$filter_key]['condition'] ?? 'eq';

                if(!in_array($condition, ['q', 'eq']))
                    continue;

                $model->orWhere($filter, 'ilike', "%".request()->get('s')."%");
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
        $filters = request()->get('filter', []);
        $filters = collect($filters)->filter(function ($filter, $key){
            return in_array($key, $this->acceptedFilters);
        });

        return $filters->toArray();
    }

}
