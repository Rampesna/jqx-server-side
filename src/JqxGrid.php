<?php

namespace Rampesna;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JqxGrid
{
    private $tableName;

    private $columns;

    private $request;

    public function __construct(
        string  $tableName,
        array   $columns,
        Request $request
    )
    {
        $this->tableName = $tableName;
        $this->columns = $columns;
        $this->request = $request;
    }

    public function initialize()
    {
        $pageNum = $this->request->pagenum;
        $pageSize = $this->request->pagesize;
        $start = $pageNum * $pageSize;

        $filtersCount = $this->request->filterscount ?? 0;
        $conditions = [];

        for ($i = 0; $i < $filtersCount; $i++) {
            $filterValue = $this->request->input("filtervalue{$i}");
            $filterCondition = $this->request->input("filtercondition{$i}");
            $filterDataField = $this->request->input("filterdatafield{$i}");
            $filterOperator = $this->request->input("filteroperator{$i}");

            $condition = '';
            $value = '';

            switch ($filterCondition) {
                case 'CONTAINS':
                    $condition = 'like';
                    $value = "%{$filterValue}%";
                    break;
                case 'DOES_NOT_CONTAIN':
                    $condition = 'not like';
                    $value = "%{$filterValue}%";
                    break;
                case 'EQUAL':
                    $condition = '=';
                    $value = $filterValue;
                    break;
                case 'NOT_EQUAL':
                    $condition = '<>';
                    $value = $filterValue;
                    break;
                case 'GREATER_THAN':
                    $condition = '>';
                    $value = $filterValue;
                    break;
                case 'LESS_THAN':
                    $condition = '<';
                    $value = $filterValue;
                    break;
                case 'GREATER_THAN_OR_EQUAL':
                    $condition = '>=';
                    $value = $filterValue;
                    break;
                case 'LESS_THAN_OR_EQUAL':
                    $condition = '<=';
                    $value = $filterValue;
                    break;
                case 'STARTS_WITH':
                    $condition = 'like';
                    $value = "{$filterValue}%";
                    break;
                case 'ENDS_WITH':
                    $condition = 'like';
                    $value = "%{$filterValue}";
                    break;
                case 'NULL':
                    $condition = 'is null';
                    break;
                case 'NOT_NULL':
                    $condition = 'is not null';
                    break;
            }

            $conditions[] = [
                'column' => $filterDataField,
                'operator' => $condition,
                'value' => $value,
                'boolean' => ($i > 0) ? $filterOperator === 0 ? 'and' : 'or' : '',
            ];
        }

        $query = DB::table(
            $this->tableName
        )->select(
            $this->columns
        );

        if (
            isset($this->request->sortdatafield) &&
            $this->request->sortorder
        ) {
            $sortField = $this->request->sortdatafield;
            $sortOrder = $this->request->sortorder;

            if ($sortField) {
                if ($sortOrder == "desc") {
                    $query->orderByDesc($sortField);
                } else if ($sortOrder == "asc") {
                    $query->orderBy($sortField);
                }
            }
        }

        foreach ($conditions as $condition) {
            $query->where($condition['column'], $condition['operator'], $condition['value'], $condition['boolean']);
        }

        $totalRows = $query->count();
        $rows = $query->skip($start)->take($pageSize)->get();

        return [
            'TotalRows' => $totalRows,
            'Rows' => $rows,
        ];
    }
}
