@extends('admin.layouts.app')

@section('title')
    Pemesanan
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
        <div class="card-body ">
            <form data-id="{{ $action }}">
                <div class="row gy-3">
                    <input type="hidden" value="{{ isset($po) ? $po->id : '' }}" id="id" name="id">
                    <div class="col">
                        <label class="form-label">Tanggal Pemesanan</label>
                        <div class=" position-relative">
                            <input class="form-control radius-8 bg-base datepicker" id="posting_date" name="posting_date"
                                type="text" value="{{ isset($po) ? $po->posting_date : '' }}"
                                {{ isset($po) ? 'disabled' : '' }}>
                            <span
                                class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon
                                    icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
                        </div>
                    </div>
                    @if(isset($po))
                        <div class="col">
                            <label class="form-label" id="received_at">Tanggal Penerimaan</label>
                            <div class=" position-relative">
                                <input class="form-control radius-8 bg-base datepicker" id="received_at" name="received_at"
                                    type="text" value="{{ isset($po) ? $po->received_at : '' }}"
                                    {{ isset($po) ? 'disabled' : '' }}>
                                <span
                                    class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon
                                        icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
                            </div>
                        </div>
                    @endif
                    <div class="col">
                        <label class="form-label">Supplier</label>
                        @if (isset($po))
                            <input type="text" name="supplier_id" value="{{ $po->supplier_name }}" class="form-control"
                                {{ isset($po) ? 'disabled' : '' }}>
                        @else
                            <select name="supplier_id" id="supplier_id" style="width:100%;">
                            </select>
                        @endif
                    </div>
                    <div class="col-12 overflow-auto">
                        <label class="form-label" for="po_items">Materials</label>
                        <table class="table bordered-table mb-0 table-hover" id="po_items">
                            <colgroup>
                                <col style="width: 0.5rem;">
                                <col style="width: 20rem;">
                                <col style="width: 10rem;">
                                <col style="width: 10rem;">
                                <col style="width: 10rem;">
                                <col style="width: 10rem;">
                                <col style="width: 10rem;">
                                <col style="width: 10rem;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check style-check d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="check-all"
                                                {{ isset($po) ? 'disabled' : '' }} />
                                        </div>
                                    </th>
                                    <th>Material</th>
                                    <th>SS</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($po_items))
                                    @foreach ($po_items as $poi)
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input check-data" type="checkbox" disabled />
                                                </div>
                                            </td>
                                            <td>
                                                <select name="material[]" class="material-select" style="width:70%;"
                                                    disabled>
                                                    <option value="{{ $poi->item_id }}" selected="selected">
                                                        {{ $poi->item_name }}
                                                    </option>
                                                </select>
                                            </td>
                                             <td>
                                                <input type="text" name="ss[]" class="form-control numeric" value="{{ $poi->safety_stock }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="min[]" class="form-control numeric" value="{{ $poi->min }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="max[]" class="form-control numeric" value="{{ $poi->max }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="qty[]" class="form-control numeric"
                                                    value="{{ $poi->qty }}" disabled>
                                            </td>
                                            <td>
                                                <input type="text" name="price[]" class="form-control numeric"
                                                    value="{{ $poi->price }}" disabled>
                                            </td>
                                            <td>
                                                <input type="text" name="amount[]" class="form-control numeric"
                                                    value="{{ $poi->amount }}" disabled>
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
                                            <select name="material[]" class="material-select" style="width:70%;">
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="ss[]" class="form-control numeric" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="min[]" class="form-control numeric" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="max[]" class="form-control numeric" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="qty[]" class="form-control numeric qty">
                                        </td>
                                        <td>
                                            <input type="text" name="price[]" class="form-control numeric price">
                                        </td>
                                        <td>
                                            <input type="text" name="amount[]" class="form-control numeric" readonly>
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
                let {
                    po_items,
                    total
                } = get_po_items();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("po_items", JSON.stringify(po_items))
                formData.append("grand_total", total)

                $.ajax({
                    type: "POST",
                    url: "/purchase-order/" + $(this).data("id"),
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
                        window.location.href = "/purchase-order";
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

            $('#add-row').click(function(e) {
                e.preventDefault();
                let rowHtml = '<tr>' +
                    '<td>' +
                    '<div class="form-check style-check d-flex align-items-center">' +
                    '<input class="form-check-input check-data" type="checkbox" />' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<select name="material[]" class="material-select">' +
                    '</select>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="ss[]" class="form-control numeric" readonly>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="min[]" class="form-control numeric" readonly>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="max[]" class="form-control numeric" readonly>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="qty[]" class="form-control numeric qty">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="price[]" class="form-control numeric price">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="amount[]" class="form-control numeric" readonly>' +
                    '</td>' +
                    '</tr>';

                $('#po_items tbody').append(rowHtml);
                initSelect2()

            });

            $('#delete-row').click(function(e) {
                e.preventDefault();

                $('#po_items tbody tr').each(function(index) {
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

            $('#po_items').on('change', '.check-data', function() {
                showHideButton()
            });

            $('#po_items').on('change', '.qty', function() {
                let row = $(this).closest('tr');
                calculateAmount(row);
            });
            $('#po_items').on('change', '.price', function() {
                let row = $(this).closest('tr');
                calculateAmount(row);
            });
            $('#po_items').on('change', '.material-select', function() {
                let row = $(this).closest('tr');
                calculateMethod($(this).val(), row)
                // console.log($(this).val());
            });

            $('#supplier_id').select2({
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

            $('#posting_date').on('change', function () {
                 $('#po_items').find('tr').each(function () {
                    let row = $(this);
                    let selectedMaterial = $(this).find('select[name="material[]"]').val();

                    // Panggil fungsi yang sama
                    calculateMethod(selectedMaterial, row);
                });
            });

        });

        function initSelect2() {
            $('.material-select').select2({
                width: '100%',
                ajax: {
                    url: "{{ route('item.bouquet.get_data_select') }}",
                    data: function(params) {
                        return {
                            search: params.term,
                            is_material: 1
                        };
                    }
                },
                placeholder: 'Pilih Material',
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

        function get_po_items() {
            let po_items = [];
            let total = 0;
            $('#po_items tbody tr').each(function() {
                let material = $(this).find('select[name="material[]"]').val();
                let qty = $(this).find('input[name="qty[]"]').val();
                let price = $(this).find('input[name="price[]"]').val();
                let amount = $(this).find('input[name="amount[]"]').val();
                let ss = $(this).find('input[name="ss[]"]').val();
                let min = $(this).find('input[name="min[]"]').val();
                let max = $(this).find('input[name="max[]"]').val();

                if (material && qty && price && amount) {
                    po_items.push({
                        material: material,
                        qty: qty,
                        price: price,
                        amount: amount,
                        ss: ss,
                        min: min,
                        max: max,
                    });
                    total += parseInt(amount);
                }
            });

            return {
                po_items,
                total
            };
        }

        function getDatePicker(receiveID) {
            flatpickr(receiveID, {
                enableTime: false,
                dateFormat: "d/m/Y",
            });
        }

        function calculateAmount(row) {
            let qty = parseFloat(row.find('input[name="qty[]"]').val()) || 0;
            let price = parseFloat(row.find('input[name="price[]"]').val()) || 0;
            let amount = qty * price;
            row.find('input[name="amount[]"]').val(amount);
        }

        function calculateMethod(material_id, row) { 
            $.ajax({
                type: "GET",
                url: "/purchase-order/calculate_method",
                data: {
                    "material_id": material_id,
                    "posting_date": $('#posting_date').val()
                },
                dataType: "JSON",
            }).done(function(res) { 
                console.log(res);
                row.find('input[name="ss[]"]').val(res.safety_stock);
                row.find('input[name="min[]"]').val(res.min);
                row.find('input[name="max[]"]').val(res.max);
            });
        }
    </script>
@endpush
