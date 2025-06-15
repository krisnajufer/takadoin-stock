@extends('admin.layouts.app')

@section('title')
    Item
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('admin/custom/css/list.css') }}">
@endpush

@push('custom-button')
    <div class="d-flex gap-3">
        <a href="{{ route('item.create') }}" class="btn btn-primary rounded py-1 text-sm" id="new-button">
            Add Item
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
@endpush

@section('content')
    <div class="card basic-data-table">
        <div class="card-header d-flex gap-3">
            <input type="text" name="search_item" id="search_item" class="form-control h-25 search-input"
                placeholder="Nama barang">
            <select name="search_is_material" id="search_is_material" class="form-select search-input">
                <option value="">Jenis Barang</option>
                <option value="0">Barang Jadi</option>
                <option value="1">Material</option>
            </select>
        </div>
        <div class="card-body overflow-auto">
            <table class="table bordered-table mb-0 table-hover dataTable" id="dataTable">
                <thead>
                    <tr>
                        <th>
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="check-all" />
                            </div>
                        </th>
                        <th>Nama Barang</th>
                        <th>Jenis Barang</th>
                        {{-- <th>Qty</th> --}}
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('custom-script')
    <script src="{{ asset('admin/custom/js/list.js') }}"></script>
    <script>
        $(document).ready(function() {
            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('item.get_data') }}",
                    data: function(d) {
                        d.name = $('#search_item').val();
                        d.is_material = $('#search_is_material').val();
                    }
                },
                columns: [{
                        data: "id",
                        render: function(data, type, row, meta) {
                            let html =
                                '<div class="form-check style-check d-flex align-items-center">'
                            html += '<input class="form-check-input check-data" type="checkbox">'
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "is_material",
                        name: "is_material",
                        render: function(data) {
                            return data == 1 ? 'Material' : 'Barang Jadi';
                        }
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0]
                }]
            });

            $('#search_item, #search_is_material').on('keyup change', function() {
                table.draw();
            });

            table.on('change', '.check-data', function() {
                showHideButton();
            });
        });
    </script>
@endpush
