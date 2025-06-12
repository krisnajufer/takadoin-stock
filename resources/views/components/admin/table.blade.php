<table class="table bordered-table mb-0 table-hover" id="dataTable" model="{{ $model }}">
    <thead>
        <tr>
            <th>
                <div class="form-check style-check d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" id="check-all" />
                </div>
            </th>
            @if (isset($headers))
                @foreach ($headers as $header)
                    <th>{{ $header['column'] }}</th>
                @endforeach
            @endif
        </tr>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>
