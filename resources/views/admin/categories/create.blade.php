@extends('admin.layouts.app')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')
@section('page-title', isset($category) ? 'Edit Category' : 'Create Category')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.create') }}" method="POST">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                <div class="mt-1 flex items-center gap-x-3">
                    <input type="color" name="color" id="color" value="{{ old('color', $category->color ?? '#3b82f6') }}"
                        class="h-10 w-10 rounded-md border border-gray-300">
                    <input type="text" value="{{ old('color', $category->color ?? '#3b82f6') }}"
                        class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        readonly>
                </div>
                @error('color')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="is_active" class="inline-flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                        {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Active</span>
                </label>
            </div>

            <div class="flex justify-end gap-x-3">
                <a href="{{ route('admin.categories.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                    {{ isset($category) ? 'Update Category' : 'Create Category' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sync color input with text display
    const colorInput = document.querySelector('input[type="color"]');
    const colorDisplay = colorInput.nextElementSibling;
    
    colorInput.addEventListener('input', function(e) {
        colorDisplay.value = e.target.value;
    });
</script>
@endsection