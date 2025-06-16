@extends('admin.layouts.app')

@section('title')
    Material
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('admin/custom/css/list.css') }}">
@endpush

@push('custom-button')
    <div class="d-flex gap-3">
        <button class="btn btn-primary rounded py-1 text-sm" id="{{ $action }}-button">Save</button>

        {{-- <div class="dropdown d-none" id="action-button">
            <button class="btn btn-warning-600 not-active py-1 dropdown-toggle toggle-icon text-sm" type="button"
                data-bs-toggle="dropdown" aria-expanded="false"> Action </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                        href="javascript:void(0)">Cancel</a></li>
                <li><a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900"
                        href="javascript:void(0)">Delete</a></li>
            </ul>
        </div> --}}
    </div>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form data-id="{{ $action }}">
                <div class="row gy-3">
                    <input type="hidden" value="{{ isset($item) ? $item->id : '' }}" id="id" name="id">
                    <div class="col-12">
                        <label class="form-label">Nama Material</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Material"
                            value="{{ isset($item) ? $item->name : '' }}">
                    </div>
                    {{-- <div class="col-12">
                        <div class="form-switch switch-primary d-flex align-items-center gap-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_material"
                                name="is_material" checked value="1">
                            <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                for="is_material">Bahan
                                Material</label>
                        </div>
                    </div> --}}
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        $(document).ready(function() {
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
                formData.append("is_material", 1);

                $.ajax({
                    type: "POST",
                    url: "/item/material/" + $(this).data("id"),
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
                        window.location.href = "/item/material";
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
