@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Edit Clinic</h1>

    <form id="updateClinicForm" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Clinic Details Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Clinic Details</h2>
            <div class="space-y-3 md:space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Name --}}
                    <div>
                        <label for="Name" class="block text-sm font-medium text-gray-700 mb-2">Name <sup class="star">*</sup></label>
                        <input type="text" id="Name" name="Name" value="{{ $clinic->Name ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter clinic name" required>
                    </div>
                    {{-- Country --}}
                    <div>
                        <label for="CountryID" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <select id="CountryID" name="CountryID"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->CountryID }}" {{ ($clinic->CountryID ?? '') == $country->CountryID ? 'selected' : '' }}>{{ $country->CountryName }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- City --}}
                    <div>
                        <label for="City" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" id="City" name="City" value="{{ $clinic->City ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter city">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Address Line 1 --}}
                    <div>
                        <label for="Address1" class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
                        <textarea id="Address1" name="Address1" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter address line 1">{{ $clinic->Address1 ?? '' }}</textarea>
                    </div>
                    {{-- State --}}
                    <div>
                        <label for="State" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <select id="State" name="State"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->StateDesc }}" {{ ($clinic->State ?? '') == $state->StateDesc ? 'selected' : '' }}>{{ $state->StateDesc }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Address Line 2 --}}
                    <div>
                        <label for="Address2" class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                        <textarea id="Address2" name="Address2" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                  placeholder="Enter address line 2">{{ $clinic->Address2 ?? '' }}</textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Authentication Key --}}
                    <div>
                        <label for="AuthenticationKey" class="block text-sm font-medium text-gray-700 mb-2">Authentication Key</label>
                        <input type="text" id="AuthenticationKey" name="AuthenticationKey" value="{{ $clinic->AuthenticationKey ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter authentication key">
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="Email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="Email" name="Email" value="{{ $clinic->Email ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter email">
                    </div>
                    {{-- Phone --}}
                    <div>
                        <label for="Phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="Phone" name="Phone" value="{{ $clinic->Phone ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter phone number">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Fax --}}
                    <div>
                        <label for="Fax" class="block text-sm font-medium text-gray-700 mb-2">Fax</label>
                        <input type="text" id="Fax" name="Fax" value="{{ $clinic->Fax ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter fax number">
                    </div>
                    {{-- Description --}}
                    <div>
                        <label for="Description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" id="Description" name="Description" value="{{ $clinic->Description ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter clinic description">
                    </div>
                </div>
            </div>
        </div>

        {{-- FTP Details Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">FTP Details</h2>
            <div class="space-y-3 md:space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- FTP Backup Server --}}
                    <div>
                        <label for="FTPBackupServer" class="block text-sm font-medium text-gray-700 mb-2">FTP Backup Server</label>
                        <input type="text" id="FTPBackupServer" name="FTPBackupServer" value="{{ $clinic->FTPBackupServer ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter FTP backup server">
                    </div>
                    {{-- FTP UserID --}}
                    <div>
                        <label for="FTPUserID" class="block text-sm font-medium text-gray-700 mb-2">FTP UserID</label>
                        <input type="text" id="FTPUserID" name="FTPUserID" value="{{ $clinic->FTPUserID ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter FTP user ID">
                    </div>
                    {{-- FTP Password --}}
                    <div>
                        <label for="FTPPassword" class="block text-sm font-medium text-gray-700 mb-2">FTP Password</label>
                        <input type="password" id="FTPPassword" name="FTPPassword" value="{{ $clinic->FTPPassword ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter FTP password">
                    </div>
                </div>
            </div>
        </div>

        {{-- SMTP Details Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">SMTP Details</h2>
            <div class="space-y-3 md:space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Email Host --}}
                    <div>
                        <label for="EmailHost" class="block text-sm font-medium text-gray-700 mb-2">Email Host</label>
                        <input type="text" id="EmailHost" name="EmailHost" value="{{ $clinic->EmailHost ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter email host">
                    </div>
                    {{-- Email Port --}}
                    <div>
                        <label for="EmailPort" class="block text-sm font-medium text-gray-700 mb-2">Email Port</label>
                        <input type="text" id="EmailPort" name="EmailPort" value="{{ $clinic->EmailPort ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter email port">
                    </div>
                    {{-- Email UserID --}}
                    <div>
                        <label for="EmailUserid" class="block text-sm font-medium text-gray-700 mb-2">Email UserID</label>
                        <input type="text" id="EmailUserid" name="EmailUserid" value="{{ $clinic->EmailUserid ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter email user ID">
                    </div>
                    {{-- Email Password --}}
                    <div>
                        <label for="EmailPassword" class="block text-sm font-medium text-gray-700 mb-2">Email Password</label>
                        <input type="password" id="EmailPassword" name="EmailPassword" value="{{ $clinic->EmailPassword ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Enter email password">
                    </div>
                </div>
            </div>
        </div>

        {{-- Clinic Logo & Letter Head Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Clinic Logo --}}
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Clinic Logo</h2>
                    <div class="flex flex-col items-center">
                        <div class="w-40 h-40 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden mb-3" id="logoPreviewContainer">
                            @if($clinic && $clinic->ClinicLogo)
                                <img id="logoPreview" src="data:image;base64,{{ $clinic->ClinicLogo }}" alt="Clinic Logo" class="w-full h-full object-contain">
                                <div id="logoPlaceholder" class="text-center text-gray-400 hidden">
                                    <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs">No Logo</span>
                                </div>
                            @else
                                <img id="logoPreview" src="" alt="" class="hidden w-full h-full object-contain">
                                <div id="logoPlaceholder" class="text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs">No Logo</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <label for="clinic_logo" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                Upload Logo
                            </label>
                            <button type="button" onclick="removelogo()" class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors {{ $clinic && $clinic->ClinicLogo ? '' : 'hidden' }}" id="removeLogoBtn">
                                Remove
                            </button>
                        </div>
                        <input type="file" id="clinic_logo" name="clinic_logo" accept="image/*" class="hidden" onchange="previewLogo(this)">
                    </div>
                </div>

                {{-- Letter Head Header --}}
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Letter Head Header</h2>
                    <div class="flex flex-col items-center">
                        <div class="w-full h-40 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden mb-3" id="letterheadPreviewContainer">
                            @if($clinic && $clinic->ClinicLetterHeadHeader)
                                <img id="letterheadPreview" src="data:image;base64,{{ $clinic->ClinicLetterHeadHeader }}" alt="Letter Head" class="w-full h-full object-contain">
                                <div id="letterheadPlaceholder" class="text-center text-gray-400 hidden">
                                    <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs">No Letter Head</span>
                                </div>
                            @else
                                <img id="letterheadPreview" src="" alt="" class="hidden w-full h-full object-contain">
                                <div id="letterheadPlaceholder" class="text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs">No Letter Head</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <label for="clinic_letterhead" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                Upload Letter Head
                            </label>
                            <button type="button" onclick="removeLetterhead()" class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors {{ $clinic && $clinic->ClinicLetterHeadHeader ? '' : 'hidden' }}" id="removeLetterheadBtn">
                                Remove
                            </button>
                        </div>
                        <input type="file" id="clinic_letterhead" name="clinic_letterhead" accept="image/*" class="hidden" onchange="previewLetterhead(this)">
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 md:pt-6 border-t border-gray-200">
            <a href="{{ route('admin.clinic.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm order-2 sm:order-1">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-sm order-1 sm:order-2">
                Update Clinic
            </button>
        </div>
    </form>
</div>
@endsection

@section('page_js')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#logoPreview').attr('src', e.target.result).removeClass('hidden');
            $('#logoPlaceholder').addClass('hidden');
            $('#removeLogoBtn').removeClass('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removelogo() {
    $('#clinic_logo').val('');
    $('#logoPreview').attr('src', '').addClass('hidden');
    $('#logoPlaceholder').removeClass('hidden');
    $('#removeLogoBtn').addClass('hidden');
}

function previewLetterhead(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#letterheadPreview').attr('src', e.target.result).removeClass('hidden');
            $('#letterheadPlaceholder').addClass('hidden');
            $('#removeLetterheadBtn').removeClass('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeLetterhead() {
    $('#clinic_letterhead').val('');
    $('#letterheadPreview').attr('src', '').addClass('hidden');
    $('#letterheadPlaceholder').removeClass('hidden');
    $('#removeLetterheadBtn').addClass('hidden');
}

$(document).ready(function() {
    // Country change handler to load states
    $('#CountryID').on('change', function() {
        var countryId = $(this).val();
        var stateSelect = $('#State');
        
        // Clear current states
        stateSelect.empty().append('<option value="">Select State</option>');
        
        if (countryId) {
            $.ajax({
                url: "{{ route('admin.clinic.getStatesByCountry', ':countryId') }}".replace(':countryId', countryId),
                type: 'GET',
                success: function(response) {
                    $.each(response, function(index, state) {
                        stateSelect.append('<option value="' + state.StateDesc + '">' + state.StateDesc + '</option>');
                    });
                    // Re-select the current state if it exists
                    var currentState = "{{ $clinic->State ?? '' }}";
                    if (currentState) {
                        stateSelect.val(currentState);
                    }
                },
                error: function() {
                    console.error('Error loading states');
                }
            });
        }
    });

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

    $("#updateClinicForm").validate({
        ignore: [],
        rules: {
            Name: { required: true, maxlength: 255 },
            Email: { email: true },
        },
        messages: {
            Name: { required: "Please enter clinic name" },
            Email: { email: "Please enter a valid email" }
        },
        submitHandler: function(form) {
            return false;
        }
    });
});

$('#updateClinicForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.clinic.update', $clinic->ClinicID) }}",
            type: "PUT",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Success!', res.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.clinic.index') }}";
                    });
                } else {
                    Swal.fire('Error!', res.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating clinic.';
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
