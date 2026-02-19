@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

    <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center px-3 md:px-4 py-2 mb-4 md:mb-6 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm md:text-base">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Doctors
    </a>

    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Add Doctor</h1>

    <form id="createDoctorForm" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Doctor Information Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Doctor Information</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-3 md:space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Name --}}
                        <div>
                            <label for="ProviderName" class="block text-sm font-medium text-gray-700 mb-2">Name <sup class="star">*</sup></label>
                            <input type="text" id="ProviderName" name="ProviderName"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Dr." required>
                        </div>
                        {{-- Location --}}
                        <div>
                            <label for="Location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="Location" name="Location"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter location">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Phone --}}
                        <div>
                            <label for="PhoneNumber" class="block text-sm font-medium text-gray-700 mb-2">Phone No.</label>
                            <input type="text" id="PhoneNumber" name="PhoneNumber"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="10" maxlength="10">
                        </div>
                        {{-- Email --}}
                        <div>
                            <label for="Email" class="block text-sm font-medium text-gray-700 mb-2">Email <sup class="star">*</sup></label>
                            <input type="email" id="Email" name="Email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter email address" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Experience --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="experience_years" name="experience_years" min="0" max="99" value="0"
                                       class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-center">
                                <span class="text-sm text-gray-600">(years)</span>
                                <input type="number" id="experience_months" name="experience_months" min="0" max="11" value="0"
                                       class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-center">
                                <span class="text-sm text-gray-600">(months)</span>
                            </div>
                        </div>
                        {{-- Rank --}}
                        <div>
                            <label for="Sequence" class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                             <input type="text" id="Sequence" name="Sequence" min="0" value="{{ $doctor->Sequence ?? 0 }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter rank" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="1" maxlength="3">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Registration Number --}}
                        <div>
                            <label for="RegistrationNumber" class="block text-sm font-medium text-gray-700 mb-2">Registration No.</label>
                            <input type="text" id="RegistrationNumber" name="RegistrationNumber"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter registration number">
                        </div>
                        {{-- Category --}}
                        <div>
                            <label for="Category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="Category" name="Category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="General">General</option>
                                <option value="Orthodontist">Orthodontist</option>
                                <option value="Endodontist">Endodontist</option>
                                <option value="Periodontist">Periodontist</option>
                                <option value="Prosthodontist">Prosthodontist</option>
                                <option value="Oral Surgeon">Oral Surgeon</option>
                                <option value="Pediatric">Pediatric</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Incentive Type --}}
                        <div>
                            <label for="IncentiveType" class="block text-sm font-medium text-gray-700 mb-2">Incentive Type</label>
                            <select id="IncentiveType" name="IncentiveType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="1">Fixed</option>
                                <option value="2">Percentage</option>
                            </select>
                        </div>
                        {{-- Incentive Value --}}
                        <div>
                            <label for="IncentiveValue" class="block text-sm font-medium text-gray-700 mb-2">Incentive Value</label>
                            <input type="text" id="IncentiveValue" name="IncentiveValue" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter incentive value" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="1" maxlength="3">
                        </div>
                    </div>
                    {{-- Color Code --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ColorCode" class="block text-sm font-medium text-gray-700 mb-2">Color Code</label>
                            <input type="color" id="ColorCode" name="ColorCode" value="#3B82F6"
                                   class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer">
                        </div>
                        <div>
                            <label for="CabinNumber" class="block text-sm font-medium text-gray-700 mb-2">Cabin Number</label>
                             <input type="text" id="CabinNumber" name="CabinNumber" min="0" value="{{ $doctor->CabinNumber ?? 1 }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter cabin number" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="1" maxlength="5">
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="DisplayInAppointmentsView" value="1" checked
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Display in Appointments View</span>
                            </label>
                        </div>
                    </div>
                </div>
                {{-- Right Column - Image Upload --}}
                <div class="flex flex-col items-center">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Doctor Image</label>
                    <div class="w-40 h-40 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden mb-3" id="imagePreviewContainer">
                        <img id="imagePreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <div id="imagePlaceholder" class="text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-xs">No Image</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <label for="provider_image" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                            Upload Image
                        </label>
                        <button type="button" onclick="removeImage()" class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors hidden" id="removeImageBtn">
                            Remove
                        </button>
                    </div>
                    <input type="file" id="provider_image" name="provider_image" accept="image/*" class="hidden" onchange="previewImage(this)">
                </div>
            </div>
        </div>

        {{-- Appointment Timing Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Appointment Timing</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                    <input type="time" id="start_time" name="start_time" value="08:30"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="22:00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label for="slot_duration" class="block text-sm font-medium text-gray-700 mb-2">Slot Duration (minutes)</label>
                    <input type="number" id="slot_duration" name="slot_duration" min="5" max="120" value="15"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="15">
                </div>
            </div>
        </div>

        {{-- User Information Section --}}
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">User Information <span class="text-xs font-normal text-gray-500">(Optional - Create login credentials for this doctor)</span></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label for="login_id" class="block text-sm font-medium text-gray-700 mb-2">Login ID (Email)<sup class="star">*</sup></label>
                    <input type="email" id="login_id" name="login_id"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Enter login email" readonly>
                </div>
                <div>
                    <label for="login_password" class="block text-sm font-medium text-gray-700 mb-2">Password <sup class="star">*</sup><span class="text-xs text-gray-500">(Min. 6 Characters)</span></label>
                    <input type="password" id="login_password" name="login_password" minlength="6"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Enter password" required>
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
                Save Doctor
            </button>
        </div>
    </form>
</div>
@endsection

@section('page_js')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result).removeClass('hidden');
            $('#imagePlaceholder').addClass('hidden');
            $('#removeImageBtn').removeClass('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    $('#provider_image').val('');
    $('#imagePreview').attr('src', '').addClass('hidden');
    $('#imagePlaceholder').removeClass('hidden');
    $('#removeImageBtn').addClass('hidden');
}

$(document).ready(function() {
    // Auto-fill login_id when email is typed
    $('#Email').on('keyup', function() {
        $('#login_id').val($(this).val());
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
            input.error, select.error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
            }
        `)
        .appendTo('head');

    $("#createDoctorForm").validate({
        ignore: [],
        rules: {
            ProviderName: { required: true },
            Email: { required: true, email: true },
            experience_years: { required: true, min: 0 },
            login_id: { 
                required: function(element) {
                    return $('#login_password').val().length > 0;
                }, 
                email: true 
            },
            login_password: { 
                required: function(element) {
                    return $('#login_id').val().length > 0;
                }, 
                minlength: 6 
            }
        },
        messages: {
            ProviderName: { required: "Please enter doctor name" },
            Email: { required: "Please enter email", email: "Please enter a valid email" },
            experience_years: { required: "Please enter experience years" },
            login_id: { required: "Please enter login ID", email: "Please enter a valid email" },
            login_password: { minlength: "Password must be at least 6 characters" }
        },
        submitHandler: function(form) {
            return false;
        }
    });
});

$('#createDoctorForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.doctors.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Success!', res.message, 'success').then(() => {
                        window.location.href = "{{ route('admin.doctors.index') }}";
                    });
                } else {
                    Swal.fire('Error!', res.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating doctor.';
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
