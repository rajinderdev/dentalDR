@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

    <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center px-3 md:px-4 py-2 mb-4 md:mb-6 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm md:text-base">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Doctors
    </a>

    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Edit Doctor</h1>

    @php
        $expYears = 0;
        $expMonths = 0;
        if ($doctor->Experience) {
            preg_match('/(\d+)\s*years?/', $doctor->Experience, $yMatch);
            preg_match('/(\d+)\s*months?/', $doctor->Experience, $mMatch);
            $expYears = $yMatch[1] ?? 0;
            $expMonths = $mMatch[1] ?? 0;
        }
    @endphp

    <form id="editDoctorForm" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

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
                            <input type="text" id="ProviderName" name="ProviderName" value="{{ $doctor->ProviderName }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Dr." required>
                        </div>
                        {{-- Location --}}
                        <div>
                            <label for="Location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="Location" name="Location" value="{{ $doctor->Location }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter location">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Phone --}}
                        <div>
                            <label for="PhoneNumber" class="block text-sm font-medium text-gray-700 mb-2">Phone No.</label>
                            <input type="text" id="PhoneNumber" name="PhoneNumber" value="{{ $doctor->PhoneNumber }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="10" maxlength="10">
                        </div>
                        {{-- Email --}}
                        <div>
                            <label for="Email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="Email" name="Email" value="{{ $doctor->Email }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter email address">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Experience --}}
                           <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="experience_years" name="experience_years" min="0" max="99" value="{{ $expYears }}"
                                       class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-center">
                                <span class="text-sm text-gray-600">(years)</span>
                                <input type="number" id="experience_months" name="experience_months" min="0" max="11" value="{{ $expMonths }}"
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
                            <input type="text" id="RegistrationNumber" name="RegistrationNumber" value="{{ $doctor->RegistrationNumber }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter registration number">
                        </div>
                        {{-- Category --}}
                        <div>
                            <label for="Category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="Category" name="Category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="General" {{ $doctor->Category == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Orthodontist" {{ $doctor->Category == 'Orthodontist' ? 'selected' : '' }}>Orthodontist</option>
                                <option value="Endodontist" {{ $doctor->Category == 'Endodontist' ? 'selected' : '' }}>Endodontist</option>
                                <option value="Periodontist" {{ $doctor->Category == 'Periodontist' ? 'selected' : '' }}>Periodontist</option>
                                <option value="Prosthodontist" {{ $doctor->Category == 'Prosthodontist' ? 'selected' : '' }}>Prosthodontist</option>
                                <option value="Oral Surgeon" {{ $doctor->Category == 'Oral Surgeon' ? 'selected' : '' }}>Oral Surgeon</option>
                                <option value="Pediatric" {{ $doctor->Category == 'Pediatric' ? 'selected' : '' }}>Pediatric</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Incentive Type --}}
                        <div>
                            <label for="IncentiveType" class="block text-sm font-medium text-gray-700 mb-2">Incentive Type</label>
                            <select id="IncentiveType" name="IncentiveType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <option value="1" {{ $doctor->IncentiveType == 1 ? 'selected' : '' }}>Fixed</option>
                                <option value="2" {{ $doctor->IncentiveType == 2 ? 'selected' : '' }}>Percentage</option>
                            </select>
                        </div>
                        {{-- Incentive Value --}}
                        <div>
                            <label for="IncentiveValue" class="block text-sm font-medium text-gray-700 mb-2">Incentive Value</label>
                            <input type="text" id="IncentiveValue" name="IncentiveValue" step="0.01" min="0" value="{{ $doctor->IncentiveValue }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   placeholder="Enter incentive value" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="1" maxlength="3">
                        </div>
                    </div>
                    {{-- Color Code --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ColorCode" class="block text-sm font-medium text-gray-700 mb-2">Color Code</label>
                            <input type="color" id="ColorCode" name="ColorCode" value="{{ $doctor->ColorCode ?? '#3B82F6' }}"
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
                                <input type="checkbox" name="DisplayInAppointmentsView" value="1" {{ $doctor->DisplayInAppointmentsView ? 'checked' : '' }}
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
                        @if($doctor->ProviderImage)
                            <img id="imagePreview" src="data:image;base64,{{ $doctor->ProviderImage }}" alt="Doctor Image" class="w-full h-full object-cover">
                            <div id="imagePlaceholder" class="text-center text-gray-400 hidden">
                                <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-xs">No Image</span>
                            </div>
                        @else
                            <img id="imagePreview" src="" alt="" class="hidden w-full h-full object-cover">
                            <div id="imagePlaceholder" class="text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-xs">No Image</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <label for="provider_image" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                            Upload Image
                        </label>
                        <button type="button" onclick="removeImage()" class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors {{ $doctor->ProviderImage ? '' : 'hidden' }}" id="removeImageBtn">
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
                    <input type="time" id="start_time" name="start_time" value="{{ $doctor->Attribute1 ?? '08:30' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="{{ $doctor->Attribute2 ?? '22:00' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label for="slot_duration" class="block text-sm font-medium text-gray-700 mb-2">Slot Duration (minutes)</label>
                    <input type="number" id="slot_duration" name="slot_duration" min="5" max="120" value="{{ $doctor->Attribute3 ?? 15 }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="15">
                </div>
            </div>
        </div>

        {{-- Linked User Info (Read-only) --}}
        @if($doctor->UserID)
        <div class="border border-gray-200 rounded-lg p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Linked User Account</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Login ID</label>
                    <input type="text" value="{{ $doctor->user->Email ?? 'N/A' }}" disabled
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User Name</label>
                    <input type="text" value="{{ $doctor->user->Name ?? 'N/A' }}" disabled
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500">
                </div>
            </div>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 md:pt-6 border-t border-gray-200">
            <button type="button" onclick="window.history.back()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm order-2 sm:order-1">
                Cancel
            </button>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-sm order-1 sm:order-2">
                Update Doctor
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

    $("#editDoctorForm").validate({
        ignore: [],
        rules: {
            ProviderName: { required: true },
            Email: { email: true }
        },
        messages: {
            ProviderName: { required: "Please enter doctor name" },
            Email: { email: "Please enter a valid email" }
        },
        submitHandler: function(form) {
            return false;
        }
    });
});

$('#editDoctorForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.doctors.update', $doctor->ProviderID) }}",
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
                        window.location.href = "{{ route('admin.doctors.index') }}";
                    });
                } else {
                    Swal.fire('Error!', res.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating doctor.';
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
