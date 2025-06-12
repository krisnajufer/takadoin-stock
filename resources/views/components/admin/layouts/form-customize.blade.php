@props(['title' => '', 'class_form' => '', 'forms' => []])
<x-admin.layouts.app title="{{ $title }}">
    <x-slot:customStyle>
        <link rel="stylesheet" href="{{ asset('admin/custom/css/form.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/custom/css/select2.min.css') }}">
    </x-slot:customStyle>
    <x-slot:customButton>
        <div class="d-flex gap-3">

            <a href="{{ route(strtolower($title) . '.index') }}" class="btn btn-secondary rounded py-1 text-sm"
                id="back-button">Back</a>
            <button class="btn btn-primary rounded py-1 text-sm" id="new-button">Save</button>

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
    </x-slot:customButton>
    <x-admin.card class="{{ $class_form }}">
        <div class="card-body">
            {{-- <div class="row gy-3">
                @foreach ($forms as $form)
                    @if ($form['fieldtype'] == 'input')
                        <div class="col-12">
                            <label class="form-label"
                                for="{{ str_replace(' ', '_', strtolower($form['label'])) }}">{{ $form['label'] }}</label>
                            <input type="text" name="{{ str_replace(' ', '_', strtolower($form['label'])) }}"
                                id="{{ str_replace(' ', '_', strtolower($form['label'])) }}" class="form-control"
                                placeholder="+1 (555) 000-0000">
                        </div>
                    @elseif ($form['fieldtype'] == 'select')
                        <div class="col-12">
                            <label class="form-label"
                                for="{{ str_replace(' ', '_', strtolower($form['label'])) }}">{{ $form['label'] }}</label>
                            <select name="{{ str_replace(' ', '_', strtolower($form['label'])) }}"
                                id="{{ str_replace(' ', '_', strtolower($form['label'])) }}" class="form-select">
                                @foreach (explode(', ', $form['options']) as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                @endforeach
            </div> --}}
            @php
                $url = explode('/', url()->current());
            @endphp
            <form id="{{ $url[4] }}" content="{{ csrf_token() }}" model={{ $url[3] }}>
                @php
                    $inColumn = false;
                @endphp
                @if (isset($forms))
                    @foreach ($forms as $form)
                        @switch($form['fieldtype'])
                            @case('section break')
                                {{-- Tutup row sebelumnya jika sedang dalam column --}}
                                @if ($inColumn)
                </div> {{-- end .row --}}
                @php $inColumn = false; @endphp
                @endif
                <div class="row mt-2"> {{-- section break sebagai row baru --}}
                    @php $inColumn = true; @endphp
                @break

                @case('column break')
                    {{-- Tutup column sebelumnya dan mulai column baru --}}
                </div> {{-- end .col --}}
                <div class="col"> {{-- new column --}}
                @break

                @case('input')
                @case('select')

                @case('numeric')
                    {{-- Mulai row dan col jika belum dimulai --}}
                    @if (!$inColumn)
                        <div class="row">
                            <div class="col">
                                @php $inColumn = true; @endphp
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="{{ str_replace(' ', '_', strtolower($form['label'])) }}">
                            {{ $form['label'] }}
                        </label>

                        @if ($form['fieldtype'] === 'input')
                            <input type="text" name="{{ str_replace(' ', '_', strtolower($form['label'])) }}"
                                id="{{ str_replace(' ', '_', strtolower($form['label'])) }}" class="form-control"
                                placeholder="{{ $form['label'] ?? '' }}">
                        @elseif ($form['fieldtype'] === 'select')
                            <select name="{{ str_replace(' ', '_', strtolower($form['label'])) }}"
                                id="{{ str_replace(' ', '_', strtolower($form['label'])) }}" class="form-select">
                                @foreach (explode(', ', $form['options']) as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif ($form['fieldtype'] === 'numeric')
                            <input type="text" name="{{ str_replace(' ', '_', strtolower($form['label'])) }}"
                                id="{{ str_replace(' ', '_', strtolower($form['label'])) }}"
                                class="form-control {{ $form['fieldtype'] }}" placeholder="{{ $form['label'] ?? '' }}">
                        @elseif ($form['fieldtype'] === 'link')
                            <select name="{{ str_replace(' ', '_', strtolower($form['label'])) }}"
                                id="{{ str_replace(' ', '_', strtolower($form['label'])) }}" class="form-select link-field"
                                data-link-to="{{ $form['options'] }}">
                            </select>
                        @endif
                    </div>
                @break
            @endswitch
            @endforeach
            @endif


            {{-- Tutup div terakhir jika masih terbuka --}}
            @if ($inColumn)
        </div> {{-- end .col --}}
        </div> {{-- end .row --}}
        @endif
        </form>
        </div>
    </x-admin.card>
    <x-slot:customScript>
        <script src="{{ asset('admin/custom/js/select2.min.js') }}"></script>
        <script src="{{ asset('admin/custom/js/form.js') }}"></script>
    </x-slot:customScript>
</x-admin.layouts.app>
