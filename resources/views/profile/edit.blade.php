@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
        <p class="text-gray-600 mt-1">Update your personal information and account settings</p>
    </div>

    <!-- Profile Form -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-lg">
        <form id="profileForm" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Profile Photo Section -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Photo</h2>
                <div class="flex items-center gap-6">
                    <!-- Current Photo -->
                    <div class="relative">
                        @if(auth()->user()->ProfilePhoto)
                            <img src="{{ asset('storage/' . auth()->user()->ProfilePhoto) }}" 
                                 alt="Profile Photo" 
                                 class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-200" id="profile_photo1">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-2xl font-semibold ring-4 ring-gray-200">
                                {{ strtoupper(substr(auth()->user()->Name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        
                        <!-- Upload Button -->
                        <label for="profile_photo" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*">
                        </label>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Upload a new profile photo</p>
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG or GIF (Max 2MB)</p>
                    </div>
                </div>
            </div>
            <!-- Personal Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ auth()->user()->Name }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ auth()->user()->Email }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ auth()->user()->Mobile ?? '' }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="+1 (555) 123-4567" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="10" maxlength="10">
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Current Password
                        </label>
                        <input type="password" id="current_password" name="current_password" autocomplete="off"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block mb-1.5 text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <input type="password" id="new_password" name="new_password" autocomplete="off"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               minlength="8">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="new_password_confirmation" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Confirm New Password
                        </label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" autocomplete="off"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ url()->previous() }}" 
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page_js')
<script>
$(document).ready(function() {
    // Form validation
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

    $("#profileForm").validate({
        ignore: [],
        rules: {
            name: { required: true, maxlength: 255 },
            email: { required: true, email: true, maxlength: 255 },
            phone: { maxlength: 20 },
            current_password: { 
                required: {
                    depends: function(element) {
                        return $("#new_password").val() !== '';
                    }
                }
            },
            new_password: { 
                minlength: 8,
                required: {
                    depends: function(element) {
                        return $("#current_password").val() !== '';
                    }
                }
            },
            new_password_confirmation: { 
                equalTo: "#new_password",
                required: {
                    depends: function(element) {
                        return $("#new_password").val() !== '';
                    }
                }
            }
        },
        messages: {
            name: { required: "Please enter your full name" },
            email: { required: "Please enter your email address", email: "Please enter a valid email address" },
            current_password: { required: "Please enter your current password" },
            new_password: { minlength: "Password must be at least 8 characters long" },
            new_password_confirmation: { equalTo: "Passwords do not match" }
        }
    });

    // Form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        if (!$('#profileForm').valid()) return;

        var formData = new FormData(this);
        formData.append('_method', 'PUT');

        $.ajax({
            url: '{{ route("profile.update") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success').then(function() {
                        // Update header user name if changed
                        if (response.user.name) {
                            $('.header-user-name').text(response.user.name);
                        }
                        // Reload page to show updated profile photo
                        if (response.user.profile_photo_url) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire('Error!', response.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    // Profile photo preview
    $('#profile_photo').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Find the profile photo container
                var photoContainer = $('#profile_photo1');
                if (photoContainer.length > 0) {
                    // Update existing image
                    photoContainer.attr('src', e.target.result);
                } else {
                    // Find the div container and replace with img
                    var divContainer = $('.relative').find('div.w-24.h-24.rounded-full');
                    if (divContainer.length > 0) {
                        divContainer.replaceWith('<img id="profile_photo1" src="' + e.target.result + '" alt="Profile Photo" class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-200">');
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
