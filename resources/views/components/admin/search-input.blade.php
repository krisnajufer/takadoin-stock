@foreach ($names as $name)
    <input type="text" name="search_{{ str_replace(' ', '_', strtolower($name)) }}"
        id="search_{{ str_replace(' ', '_', strtolower($name)) }}" class="form-control h-25 search-input"
        placeholder="{{ $name }}">
@endforeach
