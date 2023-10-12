# JqxGrid Server-Side Integration

This package has been meticulously designed to optimize JqxGrid for server-side operations for Laravel and other PHP
projects. With this integration, users can now seamlessly manage vast datasets and improve overall performance. Harness
the full potential of JqxGrid with our specialized server-side solution.

[![Total Downloads](https://img.shields.io/packagist/dt/rampesna/jqx-server-side.svg)](https://packagist.org/packages/rampesna/jax-server-side)
![Build](https://img.shields.io/badge/build-passing-brightgreen)

## Requirements

- PHP >= 8.0
- Illuminate/Database >= 8.0
- Illuminate/Htpp >= 8.0

## Installation

```bash
composer require rampesna/jqx-server-side
```

## Usage

### Controller

```php

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

### View

```typescript

var yourGridDiv = $('#your-grid-div');

var source = {
    datatype: "json",
    datafields: [
        {name: 'id', type: 'integer'},
        // your columns ...
    ],
    cache: false,
    url: 'your_controller_url',
    beforeSend: function(xhr) {
        // if you need to send authorization token
        xhr.setRequestHeader('Authorization', 'Bearer ' + your_token);
        xhr.setRequestHeader('Accept', 'application/json');
    },
    beforeprocessing: function (data) {
        source.totalrecords = data[0].TotalRows;
    },
    root: 'Rows',
    filter: function () {
        yourGridDiv.jqxGrid('updatebounddata', 'filter');
    },
    sort: function () {
        yourGridDiv.jqxGrid('updatebounddata');
    }
};

var dataAdapter = new $.jqx.dataAdapter(source);

yourGridDiv.jqxGrid({
    // other options ...
    source: dataAdapter,
    filterable: true,
    showfilterrow: true,
    pageable: true,
    sortable: true,
    virtualmode: true,
    rendergridrows: function (params) {
        return params.data;
    },
    columns: [
        {
            text: '#',
            dataField: 'id',
            columntype: 'textbox',
        },
        // your columns ...
    ],
});

```

## License

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)