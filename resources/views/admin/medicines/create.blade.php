@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

    <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center px-3 md:px-4 py-2 mb-4 md:mb-6 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm md:text-base">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Medicines
    </a>

    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Add Medicine</h1>

    <form id="createMedicineForm" class="space-y-6">
        @csrf

        {{-- Medicine Information Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Medicine Information</h2>
            <div class="space-y-3 md:space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Medicine Name --}}
                    <div class="md:col-span-2">
                        <label for="GenericName" class="block text-sm font-medium text-gray-700 mb-2">Medicine Name <sup class="star">*</sup></label>
                        <input type="text" id="GenericName" name="GenericName"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter medicine name" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Contraindications --}}
                    <div>
                        <label for="Contraindications" class="block text-sm font-medium text-gray-700 mb-2">Contraindications</label>
                        <textarea id="Contraindications" name="Contraindications" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter contraindications"></textarea>
                    </div>
                    {{-- Interactions --}}
                    <div>
                        <label for="Interactions" class="block text-sm font-medium text-gray-700 mb-2">Interactions</label>
                        <textarea id="Interactions" name="Interactions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter interactions"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Adverse Effects --}}
                    <div>
                        <label for="AdverseEffects" class="block text-sm font-medium text-gray-700 mb-2">Adverse Effects</label>
                        <textarea id="AdverseEffects" name="AdverseEffects" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter adverse effects"></textarea>
                    </div>
                    {{-- Overdose Management --}}
                    <div>
                        <label for="OverdozeManagement" class="block text-sm font-medium text-gray-700 mb-2">Overdose Management</label>
                        <textarea id="OverdozeManagement" name="OverdozeManagement" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter overdose management"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Precautions --}}
                    <div>
                        <label for="Precautions" class="block text-sm font-medium text-gray-700 mb-2">Precautions</label>
                        <textarea id="Precautions" name="Precautions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter precautions"></textarea>
                    </div>
                    {{-- Patient Alerts --}}
                    <div>
                        <label for="PatientAlerts" class="block text-sm font-medium text-gray-700 mb-2">Patient Alerts</label>
                        <textarea id="PatientAlerts" name="PatientAlerts" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter patient alerts"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    {{-- Other Info --}}
                    <div>
                        <label for="OtherInfo" class="block text-sm font-medium text-gray-700 mb-2">Other Information</label>
                        <textarea id="OtherInfo" name="OtherInfo" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter other information"></textarea>
                    </div>
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
                Save Medicine
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
            input.error, select.error, textarea.error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
            }
        `)
        .appendTo('head');

    $("#createMedicineForm").validate({
        ignore: [],
        rules: {
            GenericName: { required: true, maxlength: 100 },
            Contraindications: { maxlength: 500 },
            Interactions: { maxlength: 500 },
            AdverseEffects: { maxlength: 500 },
            OverdozeManagement: { maxlength: 500 },
            Precautions: { maxlength: 500 },
            PatientAlerts: { maxlength: 200 },
            OtherInfo: { maxlength: 500 }
        },
        messages: {
            GenericName: { required: "Please enter medicine name", maxlength: "Medicine name cannot exceed 100 characters" }
        },
        submitHandler: function(form) {
            return false;
        }
    });
});

$('#createMedicineForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.medicines.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Success!', res.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.medicines.index') }}";
                    });
                } else {
                    Swal.fire('Error!', res.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating medicine.';
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
