<table class="table bordered-table mb-0 table-hover" id="dataTable" data-page-length='10'>
    <thead>
        <tr>
            <th scope="col">
                <div class="form-check style-check d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" id="check-all" />
                </div>
            </th>
            @if (isset($headers))
                @foreach ($headers as $header)
                    <th scope="col">{{ $header }}</th>
                @endforeach
            @endif
        </tr>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>
