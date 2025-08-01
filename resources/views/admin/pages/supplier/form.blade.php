@extends('admin.layouts.app')

@section('title')
    Supplier
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('admin/custom/css/list.css') }}">
@endpush

@push('custom-button')
    <div class="d-flex gap-3">
        <a class="btn btn-secondary rounded py-1 text-sm" href="{{ route('supplier.index') }}">Back</a>
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
                    <input type="hidden" value="{{ isset($supplier) ? $supplier->id : '' }}" id="id" name="id">
                    <div class="col-12">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Supplier"
                            value="{{ isset($supplier) ? $supplier->name : '' }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="address" class="form-control" placeholder="Masukkan Alamat"
                            value="{{ isset($supplier) ? $supplier->address : '' }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="phone" class="form-control numeric" placeholder="Masukkan Nomor"
                            value="{{ isset($supplier) ? $supplier->phone : '' }}">
                    </div>
                    {{-- <div class="col-12">
                        <div class="form-switch switch-primary d-flex align-items-center gap-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_material"
                                name="is_material" checked value="1">
                            <label class="form-check-label line-height-1 fw-medium text-secondary-light"
                                for="is_material">Bahan
                                Supplier</label>
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
                $.ajax({
                    type: "POST",
                    url: "/supplier/" + $(this).data("id"),
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
                        window.location.href = "/supplier";
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
