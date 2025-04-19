@foreach ($names as $name)
    <input type="text" name="search_{{ strtolower($name) }}" id="search_{{ strtolower($name) }}"
        class="form-control h-25 search-input" placeholder="{{ $name }}">
@endforeach
