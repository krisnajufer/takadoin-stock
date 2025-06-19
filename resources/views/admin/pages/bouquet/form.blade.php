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
    <div class="d-flex gap-3">
        <button class="btn btn-primary rounded py-1 text-sm" id="{{ $action }}-button">Save</button>
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
                            value="{{ isset($item) ? $item->name : '' }}">
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
                                            <input class="form-check-input" type="checkbox" id="check-all" />
                                        </div>
                                    </th>
                                    <th>Material</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
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
                            </tbody>
                        </table>
                        <button class="btn btn-danger rounded py-1 text-sm mt-3" id="delete-row">Delete
                            Row</button>
                        <button class="btn btn-dark rounded py-1 text-sm mt-3" id="add-row">Add
                            Row</button>
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
                var formData = new FormData(this);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("is_material", 0);

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
                    '<input type="text" name="name" class="form-control numeric">' +
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
    </script>
@endpush
