@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

    <a href="{{ route('admin.lookups.index') }}" class="inline-flex items-center px-3 md:px-4 py-2 mb-4 md:mb-6 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm md:text-base">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to LookUps
    </a>

    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Edit Item</h1>

    <form id="editLookupForm" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Item Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                {{-- Category --}}
                <div>
                    <label for="ItemCategory" class="block text-sm font-medium text-gray-700 mb-2">Category <sup class="star">*</sup></label>
                    <select id="ItemCategory" name="ItemCategory"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ $lookup->ItemCategory == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Importance --}}
                <div>
                    <label for="Importance" class="block text-sm font-medium text-gray-700 mb-2">Importance</label>
                    <input type="number" id="Importance" name="Importance" min="0" value="{{ $lookup->Importance ?? 0 }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="0">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-4">
                {{-- Title --}}
                <div>
                    <label for="ItemTitle" class="block text-sm font-medium text-gray-700 mb-2">Title <sup class="star">*</sup></label>
                    <input type="text" id="ItemTitle" name="ItemTitle" value="{{ $lookup->ItemTitle }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Enter item title" required>
                </div>
                {{-- Description --}}
                <div>
                    <label for="ItemDescription" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <input type="text" id="ItemDescription" name="ItemDescription" value="{{ $lookup->ItemDescription }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Enter item description">
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 md:pt-6 border-t border-gray-200">
            <button type="button" onclick="window.history.back()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm order-2 sm:order-1">
                Cancel
            </button>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-sm order-1 sm:order-2">
                Update
            </button>
        </div>
    </form>
</div>
@endsection

@section('page_js')
<script>
$(document).ready(function() {
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            label.error {
                color: #dc3545 !important;
                font-size: 0.875rem !important;
                margin-top: 0.25rem !important;
                display: block !important;
            }
            input.error, select.error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
            }
        `)
        .appendTo('head');

    $("#editLookupForm").validate({
        ignore: [],
        rules: {
            ItemTitle: { required: true },
            ItemCategory: { required: true }
        },
        messages: {
            ItemTitle: { required: "Please enter item title" },
            ItemCategory: { required: "Please select a category" }
        },
        submitHandler: function(form) {
            return false;
        }
    });
});

$('#editLookupForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.lookups.update', $lookup->id) }}",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire('Success!', res.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.lookups.index') }}";
                    });
                } else {
                    Swal.fire('Error!', res.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating item.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                }
                Swal.fire('Error!', errorMessage, 'error');
            }
        });
    }
});
</script>
@endsection
