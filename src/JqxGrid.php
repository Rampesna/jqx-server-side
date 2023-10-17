<?php

namespace Rampesna;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JqxGrid
{
    private $tableName;
    private $columns;
    private $request;
    private $filterConditions = [
        'CONTAINS' => ['like', '%VALUE%'],
        'DOES_NOT_CONTAIN' => ['not like', '%VALUE%'],
        'EQUAL' => ['=', 'VALUE'],
        'NOT_EQUAL' => ['<>', 'VALUE'],
        'GREATER_THAN' => ['>', 'VALUE'],
        'LESS_THAN' => ['<', 'VALUE'],
        'GREATER_THAN_OR_EQUAL' => ['>=', 'VALUE'],
        'LESS_THAN_OR_EQUAL' => ['<=', 'VALUE'],
        'STARTS_WITH' => ['like', 'VALUE%'],
        'ENDS_WITH' => ['like', '%VALUE'],
        'NULL' => ['is null', null],
        'NOT_NULL' => ['is not null', null],
    ];

    public function __construct(string $tableName, array $columns, Request $request)
    {
        $this->tableName = $tableName;
        $this->columns = $columns;
        $this->request = $request;
    }

    public function initialize()
    {
        $start = $this->request->pagenum * $this->request->pagesize;
        $conditions = $this->buildFilterConditions();

        $query = DB::table($this->tableName)->select($this->columns);

        if ($sortField = $this->request->sortdatafield) {
            $query->orderBy($sortField, $this->request->sortorder ?? 'asc');
        }

        foreach ($conditions as $condition) {
            $query->where($condition['column'], $condition['operator'], $condition['value'], $condition['boolean']);
        }

        return [
            'TotalRows' => $query->count(),
            'Rows' => $query->skip($start)->take($this->request->pagesize)->get(),
        ];
    }

    private function buildFilterConditions(): array
    {
        $filtersCount = $this->request->filterscount ?? 0;
        $conditions = [];

        for ($i = 0; $i < $filtersCount; $i++) {
            $filterCondition = $this->request->input("filtercondition{$i}");

            if (!isset($this->filterConditions[$filterCondition])) {
                continue;
            }

            [$condition, $value] = $this->filterConditions[$filterCondition];

            if ($value !== null) {
                $value = str_replace('VALUE', $this->request->input("filtervalue{$i}"), $value);
            }

            $conditions[] = [
                'column' => $this->request->input("filterdatafield{$i}"),
                'operator' => $condition,
                'value' => $value,
                'boolean' => ($i > 0) ? $this->request->input("filteroperator{$i}") === 0 ? 'and' : 'or' : '',
            ];
        }

        return $conditions;
    }
}
