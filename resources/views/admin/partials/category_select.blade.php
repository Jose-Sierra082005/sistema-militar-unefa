@php
    $categories = config('course_categories', []);
    $current = old('category', $selected ?? '');
    $known = in_array($current, $categories, true);
@endphp

<select name="category" class="form-select" required id="course-category-select">
    <option value="" disabled {{ $current === '' ? 'selected' : '' }}>Seleccione categoría</option>
    @foreach($categories as $category)
        <option value="{{ $category }}" {{ $current === $category ? 'selected' : '' }}>{{ $category }}</option>
    @endforeach
    <option value="__custom__" {{ $current !== '' && !$known ? 'selected' : '' }}>Otra (personalizada)</option>
</select>

<div id="custom-category-wrap" style="margin-top: 10px; {{ $current !== '' && !$known ? '' : 'display:none;' }}">
    <input type="text" id="custom-category-input" class="form-input" placeholder="Nombre de categoría personalizada"
           value="{{ $current !== '' && !$known ? $current : '' }}">
</div>

<script>
    (function () {
        const select = document.getElementById('course-category-select');
        const wrap = document.getElementById('custom-category-wrap');
        const custom = document.getElementById('custom-category-input');
        if (!select || !wrap || !custom) return;

        function syncCustom() {
            const isCustom = select.value === '__custom__';
            wrap.style.display = isCustom ? 'block' : 'none';
            custom.required = isCustom;
            if (isCustom) {
                custom.name = 'category';
                select.removeAttribute('name');
            } else {
                custom.removeAttribute('name');
                select.name = 'category';
            }
        }

        select.addEventListener('change', syncCustom);
        syncCustom();
    })();
</script>
