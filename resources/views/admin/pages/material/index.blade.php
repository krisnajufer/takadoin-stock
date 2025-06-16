@extends('admin.layouts.app')

@section('title')
    Material
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('admin/custom/css/list.css') }}">
@endpush

@push('custom-button')
    <div class="d-flex gap-3">
        <a href="{{ route('item.material.create') }}" class="btn btn-primary rounded py-1 text-sm" id="new-button">
            Add Material
        </a>

        <div class="dropdown d-none" id="action-button">
            <button class="btn btn-warning-600 not-active py-1 dropdown-toggle toggle-icon text-sm" type="button"
                data-bs-toggle="dropdown" aria-expanded="false"> Action </button>
            <ul class="dropdown-menu">
                {{-- <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                        href="javascript:void(0)">Cancel</a></li> --}}
                <li><button
                        class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                        id="delete-button">Delete</button>
                </li>
            </ul>
        </div>
    </div>
@endpush

@section('content')
    <div class="card basic-data-table">
        <div class="card-header d-flex gap-3">
            <div>
                <input type="text" name="search_item" id="search_item" class="form-control h-25 search-input"
                    placeholder="Nama Material">
            </div>
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
                        <th>Nama Material</th>
                        {{-- <th>Jenis Material</th> --}}
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
                bLengthChange: false,
                bFilter: false,
                ajax: {
                    url: "{{ route('item.material.get_data') }}",
                    data: function(d) {
                        d.name = $('#search_item').val();
                        d.is_material = 1;
                    }
                },
                columns: [{
                        data: "id",
                        render: function(data, type, row, meta) {
                            let html =
                                '<div class="form-check style-check d-flex align-items-center">'
                            html +=
                                '<input class="form-check-input check-data" type="checkbox" value="' +
                                data + '">'
                            html += '</div>'
                            return html
                        }
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0]
                }]
            });

            $('#search_item').on('keyup', function() {
                table.draw();
            });

            table.on('change', '.check-data', function() {
                showHideButton();
            });

            $('#dataTable tbody').on('click', 'tr', function(e) {
                if ($(e.target).closest('.check-data').length > 0) {
                    return;
                }

                let rowData = table.row(this).data();
                window.location.href = "/item/material/edit/" + rowData.id

            });

            $('#delete-button').click(function(e) {
                e.preventDefault();
                let values = [];
                $('.check-data:checked').each(function(idx, el) {
                    values.push(el.value);

                });
                $.ajax({
                    type: "POST",
                    url: "/item/material/destroy",
                    data: JSON.stringify({
                        data: values
                    }),
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    contentType: "application/json",
                    dataType: "json",
                    processData: false,
                    cache: false,
                    contentType: false
                }).done(function(resp) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: resp,
                        timer: 3000,
                        showConfirmButton: false, // agar tidak ada tombol OK
                        timerProgressBar: true
                    }).then(() => {
                        table.draw()
                    });
                }).fail(function(resp) {
                    Swal.fire({
                        icon: "error",
                        title: "Warning",
                        text: resp,
                        timer: 3000
                    });
                });
            });
        });
    </script>
@endpush
