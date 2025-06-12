@php
    $columns = [
        [
            'column' => 'ID',
        ],
        [
            'column' => 'Name',
        ],
        [
            'column' => 'Gender',
        ],
        [
            'column' => 'Phone',
        ],
    ];

    $search = ['ID', 'Name', 'Phone'];
@endphp

<x-admin.layouts.list title="{{ $title }}" :search_input="$search" :table_columns="$columns"
    model="{{ str_replace(' ', '-', strtolower($title)) }}">

</x-admin.layouts.list>
