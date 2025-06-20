@extends('admin.layouts.app')

@section('title')
    Bouquet
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
        <a class="btn btn-secondary rounded py-1 text-sm" href="{{ route('item.bouquet.index') }}">Back</a>
        @if (!isset($item))
            <button class="btn btn-primary rounded py-1 text-sm" id="{{ $action }}-button">Save</button>
        @endif
    </div>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form data-id="{{ $action }}">
                <div class="row gy-3">
                    <input type="hidden" value="{{ isset($item) ? $item->id : '' }}" id="id" name="id">
                    <div class="col-12">
                        <label class="form-label">Nama Bouquet</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Bouquet"
                            value="{{ isset($item) ? $item->name : '' }}" {{ isset($item) ? 'readonly' : '' }}>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="bom">BOM</label>
                        <table class="table bordered-table mb-0 table-hover" id="bom">
                            <colgroup>
                                <col style="width: 0.5rem;">
                                <col style="width: 3rem;">
                                <col style="width: 2rem;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check style-check d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" id="check-all"
                                                {{ isset($item) ? 'disabled' : '' }} />
                                        </div>
                                    </th>
                                    <th>Material</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($boms))
                                    @foreach ($boms as $bom)
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input check-data" type="checkbox" disabled />
                                                </div>
                                            </td>
                                            <td>
                                                <select name="material[]" class="material-select" style="width:70%;"
                                                    disabled>
                                                    <option value="{{ $bom->item_id }}" selected="selected">
                                                        {{ $bom->item_name }}
                                                    </option>
                                                </select>
                                                {{-- <input type="text" name="material[]" class="form-control numeric"
                                                    value="{{ $bom->item_name }}" disabled> --}}
                                            </td>
                                            <td>
                                                <input type="text" name="qty[]" class="form-control numeric"
                                                    value="{{ $bom->qty }}" disabled>
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
                                            <input type="text" name="qty[]" class="form-control numeric">
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                        @if (!isset($item))
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
                const bom_items = get_bom_items();
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("is_material", 0);
                formData.append("bom_items", JSON.stringify(bom_items))

                $.ajax({
                    type: "POST",
                    url: "/item/bouquet/" + $(this).data("id"),
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
                        window.location.href = "/item/bouquet";
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
                    '<input type="text" name="qty[]" class="form-control numeric">' +
                    '</td>' +
                    '</tr>';

                $('#bom tbody').append(rowHtml);
                initSelect2()

            });

            $('#delete-row').click(function(e) {
                e.preventDefault();

                $('#bom tbody tr').each(function(index) {
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

            $('#bom').on('change', '.check-data', function() {
                showHideButton()
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

        function get_bom_items() {
            let bom_items = [];

            $('#bom tbody tr').each(function() {
                let material = $(this).find('select[name="material[]"]').val();
                let qty = $(this).find('input[name="qty[]"]').val();

                if (material && qty) {
                    bom_items.push({
                        material: material,
                        qty: qty
                    });
                }
            });

            return bom_items;
        }
    </script>
@endpush
