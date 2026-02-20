@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

    <a href="{{ route('admin.rx-templates.index') }}" class="inline-flex items-center px-3 md:px-4 py-2 mb-4 md:mb-6 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm md:text-base">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Rx-Templates
    </a>

    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Add Rx-Template</h1>

    <form id="createRxTemplateForm" class="space-y-6">
        @csrf

        {{-- Template Information Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Template Information</h2>
            <div class="space-y-3 md:space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Template Name --}}
                    <div class="md:col-span-2">
                        <label for="PrescriptionTemplateName" class="block text-sm font-medium text-gray-700 mb-2">Template Name <sup class="star">*</sup></label>
                        <input type="text" id="PrescriptionTemplateName" name="PrescriptionTemplateName"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter template name" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Description --}}
                    <div>
                        <label for="PrescriptionTemplateDesc" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="PrescriptionTemplateDesc" name="PrescriptionTemplateDesc" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter template description"></textarea>
                    </div>
                    {{-- Prescription Note --}}
                    <div>
                        <label for="PrescriptionNote" class="block text-sm font-medium text-gray-700 mb-2">Prescription Note</label>
                        <textarea id="PrescriptionNote" name="PrescriptionNote" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter prescription note"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Medicines Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-2">
                <h2 class="text-lg font-semibold text-gray-700">Template Medicines</h2>
                <button type="button" onclick="addMedicineRow()" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                    <i class="fas fa-plus mr-1"></i> Add Medicine
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full" id="medicines-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Medicine Name <sup class="star">*</sup></th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Frequency</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Dosage</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Duration</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Drug Note</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody id="medicines-body">
                    </tbody>
                </table>
            </div>

            <div id="no-medicines-msg" class="text-center py-4 text-gray-400 text-sm">
                No medicines added yet. Click "Add Medicine" to begin.
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
                Save Rx-Template
            </button>
        </div>
    </form>
</div>
@endsection

@section('page_js')
<script>
var medicineRowIndex = 0;
var medicinesList = @json($medicines);

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

    $("#createRxTemplateForm").validate({
        ignore: [],
        rules: {
            PrescriptionTemplateName: { required: true, maxlength: 255 },
            PrescriptionTemplateDesc: { maxlength: 500 },
            PrescriptionNote: { maxlength: 500 }
        },
        messages: {
            PrescriptionTemplateName: { required: "Please enter template name", maxlength: "Template name cannot exceed 255 characters" }
        },
        submitHandler: function(form) {
            return false;
        }
    });
});

function addMedicineRow(data) {
    data = data || {};
    var row = `
        <tr class="medicine-row border-b border-gray-100" data-index="${medicineRowIndex}">
            <td class="px-3 py-2 text-sm text-gray-600 row-number">${medicineRowIndex + 1}</td>
            <td class="px-3 py-2">
                <input type="hidden" name="template_medicines[${medicineRowIndex}][MedicineId]" value="${data.MedicineId || ''}">
                <input type="text" name="template_medicines[${medicineRowIndex}][MedicineName]" value="${data.MedicineName || ''}"
                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 medicine-name-input"
                       placeholder="Type medicine name..." list="medicine-list-${medicineRowIndex}" required>
                <datalist id="medicine-list-${medicineRowIndex}">
                    ${medicinesList.map(m => '<option value="' + m.GenericName + '" data-id="' + m.DrugId + '">').join('')}
                </datalist>
            </td>
            <td class="px-3 py-2">
                <input type="text" name="template_medicines[${medicineRowIndex}][Frequency]" value="${data.Frequency || ''}"
                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                       placeholder="e.g., Twice daily">
            </td>
            <td class="px-3 py-2">
                <input type="text" name="template_medicines[${medicineRowIndex}][Dosage]" value="${data.Dosage || ''}"
                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                       placeholder="e.g., 500mg">
            </td>
            <td class="px-3 py-2">
                <input type="text" name="template_medicines[${medicineRowIndex}][Duration]" value="${data.Duration || ''}"
                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                       placeholder="e.g., 5 days">
            </td>
            <td class="px-3 py-2">
                <input type="text" name="template_medicines[${medicineRowIndex}][DrugNote]" value="${data.DrugNote || ''}"
                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                       placeholder="Note">
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeMedicineRow(this)" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;

    $('#medicines-body').append(row);
    medicineRowIndex++;
    updateNoMedicinesMsg();
    updateRowNumbers();

    // Bind medicine name change to set MedicineId
    $('#medicines-body tr:last .medicine-name-input').on('change', function() {
        var val = $(this).val();
        var row = $(this).closest('tr');
        var matched = medicinesList.find(m => m.GenericName === val);
        row.find('input[name$="[MedicineId]"]').val(matched ? matched.DrugId : '');
    });
}

function removeMedicineRow(btn) {
    $(btn).closest('tr').remove();
    updateNoMedicinesMsg();
    updateRowNumbers();
}

function updateRowNumbers() {
    $('#medicines-body .medicine-row').each(function(index) {
        $(this).find('.row-number').text(index + 1);
    });
}

function updateNoMedicinesMsg() {
    if ($('#medicines-body .medicine-row').length === 0) {
        $('#no-medicines-msg').show();
    } else {
        $('#no-medicines-msg').hide();
    }
}

$('#createRxTemplateForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.rx-templates.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Success!', res.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.rx-templates.index') }}";
                    });
                } else {
                    Swal.fire('Error!', res.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating Rx-Template.';
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
