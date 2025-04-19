@extends('admin.layouts.app')

@section('title')
    New Customer
@endsection

@section('custom-button')
    <a href="" class="btn btn-primary rounded py-1 text-sm">Save</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body row">
            <div class="col-md-6">
                <label class="form-label">Firstname</label>
                <input type="text" name="#0" class="form-control h-50" value="info@gmail.com">
            </div>
            <div class="col-md-6">
                <label class="form-label">Lastname</label>
                <input type="text" name="#0" class="form-control  h-50" value="info@gmail.com">
            </div>
            <div class="col-md-6 mt-3">
                <label class="form-label">Phone</label>
                <input type="text" name="#0" class="form-control h-50" value="info@gmail.com">
            </div>
            <div class="col-md-6 mt-3">
                <label class="form-label">Gender</label>
                <input type="text" name="#0" class="form-control h-50" value="info@gmail.com">
            </div>
        </div>
    </div>
@endsection
