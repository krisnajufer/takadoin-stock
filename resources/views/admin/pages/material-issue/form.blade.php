@extends('admin.layouts.app')

@section('title')
    Issue
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
        <a class="btn btn-secondary rounded py-1 text-sm" href="{{ route('material-issue.index') }}">Back</a>
        @if (!isset($mtr_issue))
            <button class="btn btn-primary rounded py-1 text-sm" id="{{ $action }}-button">Save</button>
        @endif
    </div>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form data-id="{{ $action }}">
                <div class="row gy-3">
                    <input type="hidden" value="{{ isset($mtr_issue) ? $mtr_issue->id : '' }}" id="id" name="id">
                    <div class="col-6">
                        <label class="form-label" id="posting_date">Tanggal Issue</label>
                        <div class=" position-relative">
                            <input class="form-control radius-8 bg-base datepicker" id="posting_date" name="posting_date"
                                type="text" value="{{ isset($mtr_issue) ? $mtr_issue->posting_date : '' }}"
                                {{ isset($mtr_issue) ? 'disabled' : '' }}>
                            <span
                                class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon
                                    icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label" id="posting_date">Tipe Issue</label>
                        <select name="issue_type" id="issue_type" class="form-select">
                            <option value="">- Tipe Issue --</option>
                            <option value="Broken" {{ isset($mtr_issue) && $mtr_issue->issue_type == 'Broken' ? 'selected' : ''  }}>Rusak</option>
                            <option value="Expired" {{ isset($mtr_issue) && $mtr_issue->issue_type == 'Expired' ? 'selected' : ''  }}>Expired</option>
                            <option value="Lost" {{ isset($mtr_issue) && $mtr_issue->issue_type == 'Lost' ? 'selected' : ''  }}>Hilang</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="issue_items">Materials</label>
                        <table class="table bordered-table mb-0 table-hover" id="issue_items">
                            <colgroup>
                                <col style="width: 0.5rem;">
                                <col style="width: 10rem;">
                                <col style="width: 2rem;">
                                <col style="width: 2rem;">
                                <col style="width: 2rem;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check style-check d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="check-all"
                                                {{ isset($mtr_issue) ? 'disabled' : '' }} />
                                        </div>
                                    </th>
                                    <th>Material</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($issue_items))
                                    @foreach ($issue_items as $row)
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input check-data" type="checkbox" disabled />
                                                </div>
                                            </td>
                                            <td>
                                                <select name="material[]" class="material-select" style="width:70%;"
                                                    disabled>
                                                    <option value="{{ $row->item_id }}" selected="selected">
                                                        {{ $row->item_name }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="qty[]" class="form-control numeric"
                                                    value="{{ $row->qty }}" disabled>
                                            </td>
                                            <td>
                                                <input type="text" name="price[]" class="form-control numeric"
                                                    value="{{ $row->price }}" disabled>
                                            </td>
                                            <td>
                                                <input type="text" name="amount[]" class="form-control numeric"
                                                    value="{{ $row->amount }}" disabled>
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
                        @if (!isset($mtr_issue))
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
                    issue_items,
                    total
                } = get_issue_items();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("issue_items", JSON.stringify(issue_items))
                formData.append("grand_total", total)

                $.ajax({
                    type: "POST",
                    url: "/material-issue/" + $(this).data("id"),
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
                        window.location.href = "/material-issue";
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
                    '<input type="text" name="qty[]" class="form-control numeric qty">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="price[]" class="form-control numeric price">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="amount[]" class="form-control numeric" readonly>' +
                    '</td>' +
                    '</tr>';

                $('#issue_items tbody').append(rowHtml);
                initSelect2()

            });

            $('#delete-row').click(function(e) {
                e.preventDefault();

                $('#issue_items tbody tr').each(function(index) {
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

            $('#issue_items').on('change', '.check-data', function() {
                showHideButton()
            });

            $('#issue_items').on('change', '.qty', function() {
                let row = $(this).closest('tr');
                calculateAmount(row);
            });
            $('#issue_items').on('change', '.price', function() {
                let row = $(this).closest('tr');
                calculateAmount(row);
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

        function get_issue_items() {
            let issue_items = [];
            let total = 0;
            $('#issue_items tbody tr').each(function() {
                let material = $(this).find('select[name="material[]"]').val();
                let qty = $(this).find('input[name="qty[]"]').val();
                let price = $(this).find('input[name="price[]"]').val();
                let amount = $(this).find('input[name="amount[]"]').val();

                if (material && qty && price && amount) {
                    issue_items.push({
                        material: material,
                        qty: qty,
                        price: price,
                        amount: amount,
                    });
                    total += parseInt(amount);
                }
            });

            return {
                issue_items,
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
    </script>
@endpush
