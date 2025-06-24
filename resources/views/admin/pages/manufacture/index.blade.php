@extends('admin.layouts.app')

@section('title')
    Pembentukan
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('admin/custom/css/list.css') }}">
    <style>
        span.selection {
            width: 100%;
            height: 100%;
        }
    </style>
@endpush

@push('custom-button')
    <div class="d-flex gap-3">
        <a href="{{ route('manufacture.create') }}" class="btn btn-primary rounded py-1 text-sm" id="new-button">
            Add Pembentukan
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
                <input type="text" name="search_id" id="search_id" class="form-control h-25 search-input"
                    placeholder="Kode Pembentukan">
            </div>
            {{-- <div style="width:20rem;">
                <select name="search_supplier" id="search_supplier" class="search-input" style="width:100%;">
                </select>
            </div> --}}
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
                        <th>Kode Pembentukan</th>
                        <th>Tanggal Pembentukan</th>
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
                    url: "{{ route('manufacture.get_data') }}",
                    data: function(d) {
                        d.supplier = $('#search_supplier').val();
                        d.id = $('#search_id').val();
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
                        data: "id",
                        name: "id"
                    },
                    {
                        data: "posting_date",
                        name: "posting_date"
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [0]
                }]
            });

            $('.search-input').on('keyup change', function() {
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
                window.location.href = "/manufacture/edit/" + rowData.id.replaceAll("/", "-")

            });

            $('#delete-button').click(function(e) {
                e.preventDefault();
                let values = [];
                $('.check-data:checked').each(function(idx, el) {
                    values.push(el.value);

                });
                $.ajax({
                    type: "POST",
                    url: "/manufacture/destroy",
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

            // $('#search_supplier').select2({
            //     width: '100%',
            //     ajax: {
            //         url: "{{ route('supplier.get_data_select') }}",
            //         data: function(params) {
            //             return {
            //                 search: params.term,
            //             };
            //         }
            //     },
            //     placeholder: 'Pilih Supplier',
            // });
        });
    </script>
@endpush
