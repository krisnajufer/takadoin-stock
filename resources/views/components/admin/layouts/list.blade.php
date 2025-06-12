@props(['search_input' => [], 'table_columns' => [], 'title' => '', 'class_dt' => '', 'model' => ''])

<x-admin.layouts.app title="{{ $title }}">
    <x-slot:customStyle>
        <link rel="stylesheet" href="{{ asset('admin/custom/css/list.css') }}">
        
    </x-slot:customStyle>
    <x-slot:customButton>
        <div class="d-flex gap-3">
            <a href="{{ route(strtolower($title) . '.add') }}" class="btn btn-primary rounded py-1 text-sm"
                id="new-button">
                Add {{ $title }}
            </a>


            <div class="dropdown d-none" id="action-button">
                <button class="btn btn-warning-600 not-active py-1 dropdown-toggle toggle-icon text-sm" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false"> Action </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                            href="javascript:void(0)">Cancel</a></li>
                    <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                            href="javascript:void(0)">Delete</a></li>
                </ul>
            </div>
        </div>
    </x-slot>
    <x-admin.card class="basic-data-table {{ $class_dt }}">
        <div class="card-header d-flex gap-3">
            <x-admin.search-input :names="$search_input" />
        </div>
        <div class="card-body overflow-auto">
            <x-admin.table :headers="$table_columns" model="{{ $model }}">
            </x-admin.table>
        </div>
    </x-admin.card>
    <x-slot:customScript>
        <script src="{{ asset('admin/custom/js/list.js') }}"></script>
    </x-slot:customScript>
</x-admin.layouts.app>
