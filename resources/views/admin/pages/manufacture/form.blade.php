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
                                type="text" placeholder="03/12/2024" value="{{ isset($po) ? $po->posting_date : '' }}"
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
                                <col style="width: 10rem;">
                                <col style="width: 2rem;">
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
                    mnf_items,
                    total
                } = get_mnf_items();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("mnf_items", JSON.stringify(mnf_items))
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
                    '<select name="bouquet[]" class="bouquet-select">' +
                    '</select>' +
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

        function get_mnf_items() {
            let mnf_items = [];
            let total = 0;
            $('#mnf_items tbody tr').each(function() {
                let bouquet = $(this).find('select[name="bouquet[]"]').val();
                let qty = $(this).find('input[name="qty[]"]').val();

                if (bouquet && qty) {
                    mnf_items.push({
                        bouquet: bouquet,
                        qty: qty,
                        price: price,
                        amount: amount,
                    });
                }
            });

            return {
                mnf_items,
                total
            };
        }

        function getDatePicker(receiveID) {
            flatpickr(receiveID, {
                enableTime: false,
                dateFormat: "d/m/Y",
            });
        }
    </script>
@endpush
