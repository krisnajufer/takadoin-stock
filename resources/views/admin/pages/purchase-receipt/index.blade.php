@extends('admin.layouts.app')

@section('title')
    Penerimaan
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
        {{-- <a href="{{ route('purchase-receipt.create') }}" class="btn btn-primary rounded py-1 text-sm" id="new-button">
            Add Pemesanan
        </a> --}}

        <div class="dropdown d-none" id="action-button">
            <button class="btn btn-warning-600 not-active py-1 dropdown-toggle toggle-icon text-sm" type="button"
                data-bs-toggle="dropdown" aria-expanded="false"> Action </button>
            <ul class="dropdown-menu">
                {{-- <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                        href="javascript:void(0)">Cancel</a></li> --}}
                <li><button
                        class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                        id="accept-button">Terima Pemesanan</button>
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
                    placeholder="Kode Pemesanan">
            </div>
            <div style="width:20rem;">
                <select name="search_supplier" id="search_supplier" class="search-input" style="width:100%;">
                </select>
            </div>
            <div>
                <div class="position-relative d-none" id="wrap-date">
                    <input class="form-control radius-8 bg-base datepicker" id="posting_date" name="posting_date"
                        type="text" placeholder="Tanggal Penerimaan">
                    <span class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon
                            icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
                </div>
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
                        <th>Kode Pemesanan</th>
                        <th>Supplier</th>
                        <th>Tanggal Pemesanan</th>
                        <th>Status Pemesanan</th>
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
                    url: "{{ route('purchase-receipt.get_data') }}",
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
                        data: "supplier_name",
                        name: "supplier_name"
                    },
                    {
                        data: "posting_date",
                        name: "posting_date"
                    },
                    {
                        data: "status",
                        render: function(data, type, row, meta) {
                            let status = (data == 'Diterima') ? 'success' : 'secondary';
                            let html = `<span class="badge text-bg-${status}">${data}</span>`;
                            return html
                        }
                    },
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
                showHideDate();
            });

            $('#dataTable tbody').on('click', 'tr', function(e) {
                if ($(e.target).closest('.check-data').length > 0) {
                    return;
                }

                let rowData = table.row(this).data();
                window.location.href = "/purchase-receipt/edit/" + rowData.id.replaceAll("/", "-")

            });

            $('#accept-button').click(function(e) {
                e.preventDefault();
                let values = [];
                $('.check-data:checked').each(function(idx, el) {
                    values.push(el.value);

                });
                $.ajax({
                    type: "POST",
                    url: "/purchase-receipt/received",
                    data: JSON.stringify({
                        data: values,
                        posting_date: $('#posting_date').val()
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
                        showHideButton();
                        showHideDate();
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

            $('#search_supplier').select2({
                width: '100%',
                ajax: {
                    url: "{{ route('supplier.get_data_select') }}",
                    data: function(params) {
                        return {
                            search: params.term,
                        };
                    }
                },
                placeholder: 'Pilih Supplier',
            });

            getDatePicker('.datepicker')
        });

        function getDatePicker(receiveID) {
            flatpickr(receiveID, {
                enableTime: false,
                dateFormat: "d/m/Y",
            });
        }

        function showHideDate() {
            const $dateWrap = $('#wrap-date');

            if ($('.check-data:checked').length > 0) {
                $dateWrap.removeClass('d-none');
            } else {
                $dateWrap.addClass('d-none');
                $checkAll.prop('checked', false);
            }
        }
    </script>
@endpush
