@extends('admin.layouts.app')

@section('title')
    Pembentukan
@endsection

@push('custom-style')
    <style>
        span.selection {
            width: 100%;
            height: 100%;
        }
    </style>
@endpush

@push('custom-button')
    <div class="d-flex gap-2">
        <a class="btn btn-secondary rounded py-1 text-sm" href="{{ route('purchase-order.index') }}">Back</a>
        @if (!isset($po))
            <button class="btn btn-primary rounded py-1 text-sm" id="{{ $action }}-button">Save</button>
        @endif
    </div>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form data-id="{{ $action }}">
                <div class="row gy-3">
                    <input type="hidden" value="{{ isset($po) ? $po->id : '' }}" id="id" name="id">
                    <div class="col-6">
                        <label class="form-label" id="posting_date">Tanggal Pembentukan</label>
                        <div class=" position-relative">
                            <input class="form-control radius-8 bg-base datepicker" id="posting_date" name="posting_date"
                                type="text" value="{{ isset($po) ? $po->posting_date : '' }}"
                                {{ isset($po) ? 'disabled' : '' }}>
                            <span
                                class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon
                                    icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="mnf_items">Bouquets</label>
                        <table class="table bordered-table mb-0 table-hover" id="mnf_items">
                            <colgroup>
                                <col style="width: 0.5rem;">
                                <col style="width: 20rem;">
                                <col style="width: 10rem;">
                                <col style="width: 0.5rem;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check style-check d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="check-all"
                                                {{ isset($po) ? 'disabled' : '' }} />
                                        </div>
                                    </th>
                                    <th>Bouquet</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($mnf_items))
                                    @foreach ($mnf_items as $poi)
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input check-data" type="checkbox" disabled />
                                                </div>
                                            </td>
                                            <td>
                                                <select name="bouquet[]" class="bouquet-select" style="width:70%;" disabled>
                                                    <option value="{{ $poi->item_id }}" selected="selected">
                                                        {{ $poi->item_name }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="qty[]" class="form-control numeric"
                                                    value="{{ $poi->qty }}" disabled>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning rounded py-1 text-sm detail"
                                                    type="button">Calculate</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <div class="form-check style-check d-flex align-items-center">
                                                <input class="form-check-input check-data" type="checkbox" />
                                            </div>
                                        </td>
                                        <td>
                                            <select name="bouquet[]" class="bouquet-select" style="width:70%;">
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="qty[]" class="form-control numeric qty">
                                        </td>
                                        <td>
                                            <button class="btn btn-warning rounded py-1 text-sm detail"
                                                type="button">Calculate</button>
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                        @if (!isset($po))
                            <button class="btn btn-danger rounded py-1 text-sm mt-3 d-none" id="delete-row">Delete
                                Row</button>
                            <button class="btn btn-dark rounded py-1 text-sm mt-3" id="add-row">Add
                                Row</button>
                        @endif
                    </div>
                    <div class="col-12 mt-5" id="wrap-bom">
                        <label class="form-label" for="mnf_items">BOM for <span id="label-manufacture"></span></label>
                        <table class="table bordered-table mb-0 table-hover" id="bom-table">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Qty saat ini</th>
                                    <th>Qty dibutuhkan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        $(document).ready(function() {
            getDatePicker('.datepicker')
            initSelect2()
            $('.numeric').keypress(function(e) {
                var key = String.fromCharCode(e.which);
                if (!(/[0-9]/.test(key))) {
                    e.preventDefault();
                }
            });

            $('#store-button').click(function(e) {
                e.preventDefault();
                $('form').trigger('submit');
            });
            $('#update-button').click(function(e) {
                e.preventDefault();
                $('form').trigger('submit');
            });

            $('form').submit(function(e) {
                e.preventDefault()
                let mnf_items = get_mnf_items();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("mnf_items", JSON.stringify(mnf_items))

                $.ajax({
                    type: "POST",
                    url: "/manufacture/" + $(this).data("id"),
                    data: formData,
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
                        window.location.href = "/manufacture";
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
            $('#check-all').change(function(e) {
                e.preventDefault();
                checkAll($(this).is(':checked'));
            });

            $('#mnf_items tbody').on('click', '.detail', function(e) {
                let row = $(this).closest('tr');

                let bouquet = row.find('.bouquet-select').val();
                let bouquetName = row.find('.bouquet-select').select2('data')[0].text;
                
                let qty = row.find('.qty').val() || row.find('input[name="qty[]"]')
                    .val()

                if (bouquet == null || qty == '' || qty < 1) {
                    return
                }

                let data = {
                    bouquet: bouquet,
                    qty: qty
                };

                getDataBom(data, bouquetName);

            });

            $('#add-row').click(function(e) {
                e.preventDefault();
                let rowHtml = '<tr>' +
                    '<td>' +
                    '<div class="form-check style-check d-flex align-items-center">' +
                    '<input class="form-check-input check-data" type="checkbox" />' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<select name="bouquet[]" class="bouquet-select">' +
                    '</select>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="qty[]" class="form-control numeric qty">' +
                    '</td>' +
                    '<td>' +
                    '<button class="btn btn-warning rounded py-1 text-sm detail" type="button">Calculate</button>' +
                    '</td>' +
                    '</tr>';

                $('#mnf_items tbody').append(rowHtml);
                initSelect2()

            });

            $('#delete-row').click(function(e) {
                e.preventDefault();

                $('#mnf_items tbody tr').each(function(index) {
                    if (index > 0) {
                        let checkbox = $(this).find('.check-data');

                        if (checkbox.is(':checked')) {
                            $(this).remove();
                        }
                    }
                });

                $('.check-data').prop('checked', false);
                $('#check-all').prop('checked', false);
            });

            $('#mnf_items').on('change', '.check-data', function() {
                showHideButton()
            });
        });

        function initSelect2() {
            $('.bouquet-select').select2({
                width: '100%',
                ajax: {
                    url: "{{ route('item.bouquet.get_data_select') }}",
                    data: function(params) {
                        return {
                            search: params.term,
                            is_material: 0
                        };
                    }
                },
                placeholder: 'Pilih Bouquet',
            });
        }

        function checkAll(checked) {
            $('.check-data').prop('checked', checked);
            showHideButton();
        }

        function showHideButton() {
            const $delButton = $('#delete-row');

            if ($('.check-data:checked').length > 0) {
                $delButton.removeClass('d-none');
            } else {
                $delButton.addClass('d-none');
                $checkAll.prop('checked', false);
            }
        }

        function getDataBom(data, bouquetName) {
            $.ajax({
                type: "GET",
                url: "/manufacture/get_data_bom",
                data: data,
                dataType: "JSON",
            }).done(function(res) {
                const tbody = $('#bom-table tbody');
                tbody.empty(); // hapus semua isi tbody
                res.forEach(row => {
                    appendRow(tbody, row)
                });

                $('#label-manufacture').text(bouquetName);
            }).fail(function(res) {});
        }

        function appendRow(tbody, row) {
            let row_status = {'color': 'danger', 'status': 'Tidak Mencukupi'}
            if (row.current_qty > row.needed_qty) {
                row_status['color'] = 'success';
                row_status['status'] = 'Mencukupi';
            }
            let html = `
                    <tr>
                        <td>${row.material}</td>
                        <td>${row.current_qty}</td>
                        <td>${row.needed_qty}</td>
                        <td><span class="badge bg-${row_status.color}">${row_status.status}</span></td>
                    </tr>
                `;

            tbody.append(html);
        }

        function get_mnf_items() {
            let mnf_items = [];
            $('#mnf_items tbody tr').each(function() {
                let bouquet = $(this).find('select[name="bouquet[]"]').val();
                let qty = $(this).find('input[name="qty[]"]').val();

                if (bouquet && qty) {
                    mnf_items.push({
                        bouquet: bouquet,
                        qty: qty,
                    });
                }
            });

            return mnf_items;
        }

        function getDatePicker(receiveID) {
            flatpickr(receiveID, {
                enableTime: false,
                dateFormat: "d/m/Y",
            });
        }
    </script>
@endpush
