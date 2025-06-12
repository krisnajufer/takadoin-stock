@props(['title' => '', 'class_form' => ''])
<x-admin.layouts.app title="{{ $title }}">
    <x-slot:customStyle>
        <link rel="stylesheet" href="{{ asset('admin/custom/css/form.css') }}">
    </x-slot:customStyle>
    <x-slot:customButton>
        <div class="d-flex gap-3">
            <a href="" class="btn btn-primary rounded py-1 text-sm" id="new-button">Save</a>

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
    <x-admin.card class="{{ $class_form }}">
        <div class="card-body">
            {{ $slot }}
        </div>
    </x-admin.card>
</x-admin.layouts.app>
