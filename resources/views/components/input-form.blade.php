@props(['name', 'label', 'type' => 'text', 'value' => '', 'id' => null])
@php($field = $id ?? $name)
<div class="mb-3">
    <label for="{{ $field }}" class="form-label">{{ $label }}</label>
    <input type="{{ $type }}" class="form-control @error($name) is-invalid @enderror" id="{{ $field }}" name="{{ $name }}" value="{{ old($name, $value) }}" />
    <span class="error invalid-feedback">{{ $errors->first($name) }}</span>
</div>
