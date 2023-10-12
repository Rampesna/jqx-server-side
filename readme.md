# JqxGrid Server-Side Integration

This package has been meticulously designed to optimize JqxGrid for server-side operations for Laravel and other PHP
projects. With this integration, users can now seamlessly manage vast datasets and improve overall performance. Harness
the full potential of JqxGrid with our specialized server-side solution.

![Build](https://img.shields.io/badge/build-passing-brightgreen)

## Requirements

- PHP >= 8.0
- Illuminate/Database >= 8.0
- Illuminate/Htpp >= 8.0

## Installation

```
composer require rampesna/jqx-server-side
```

## Usage

### Controller

```
use Rampesna\JqxGrid;

class ExampleController extends Controller
{
    public function index(Request $request)
    {
        $tableName = 'your_table_name';
        $columns = [
            'id',
            'name',
            'surname',
            // ...
        ];
        
        $jqxGrid = new JqxGrid(
            $tableName,
            $columns,
            $request
        );
        
        return response()->json($jqxGrid->initialize());
    }
}
```

## License

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)