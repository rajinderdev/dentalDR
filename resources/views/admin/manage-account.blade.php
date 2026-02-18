@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
     <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 md:px-4 py-2 mb-4 md:mb-6 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm md:text-base">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Users
    </a>
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Manage Account</h1>
    
    <form id="manageAccountForm" class="space-y-4 md:space-y-6">
         @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
            {{-- Left Column --}}
            <div class="space-y-3 md:space-y-4">
                {{-- Login ID --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name<sup class="star mt-4">*</sup></label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter login ID"
                        value="{{ $user->Name ?? '' }}"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter password"
                    >
                </div>

              
                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Confirm password"
                    >
                </div>
                 {{-- User Type --}}
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-700 mb-2">User Type<sup class="star">*</sup></label>
                    <select 
                        id="user_type" 
                        name="user_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                        <option value="">Select user type</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->RoleID }}" {{ ($user->RoleID ?? '') == $role->RoleID ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                 {{-- Locked Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Locked</label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    name="locked" 
                                    value="1"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    {{ ($user->Locked ?? 0) == 1 ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700">Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    name="locked" 
                                    value="0"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    {{ ($user->Locked ?? 0) == 0 ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700">No</span>
                            </label>
                        </div>
                    </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-3 md:space-y-4">
                  {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail<sup class="star">*</sup></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter email address"
                        value="{{ $user->Email ?? '' }}"
                    >
                </div>
                  {{-- Mobile --}}
                 <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                    <input 
                        type="text" 
                        id="mobile" 
                        name="mobile"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter mobile number"
                        value="{{ $user->Mobile ?? '' }}"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                        minlength="10" maxlength="10"
                    >
                </div>


                {{-- Security Question --}}
                <div>
                    <label for="security_question" class="block text-sm font-medium text-gray-700 mb-2">Security Question<sup class="star">*</sup></label>
                    <select 
                        id="security_question" 
                        name="security_question"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    >
                        <option value="">Select a security question</option>
                        <option value="pet" {{ ($user->SecurityQuestion ?? '') == 'pet' ? 'selected' : '' }}>What was your first pet's name?</option>
                        <option value="school" {{ ($user->SecurityQuestion ?? '') == 'school' ? 'selected' : '' }}>What elementary school did you attend?</option>
                        <option value="city" {{ ($user->SecurityQuestion ?? '') == 'city' ? 'selected' : '' }}>In what city were you born?</option>
                        <option value="mother_maiden" {{ ($user->SecurityQuestion ?? '') == 'mother_maiden' ? 'selected' : '' }}>What is your mother's maiden name?</option>
                        <option value="first_car" {{ ($user->SecurityQuestion ?? '') == 'first_car' ? 'selected' : '' }}>What was your first car?</option>
                    </select>
                </div>
                {{-- Security Answer --}}
                <div>
                    <label for="security_answer" class="block text-sm font-medium text-gray-700 mb-2">Security Answer<sup class="star">*</sup></label>
                    <input 
                        type="text" 
                        id="security_answer" 
                        name="security_answer"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter security answer"
                        value="{{ $user->SecurityAnswer ?? '' }}"
                    >
                </div>
                {{-- Status Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Approved Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Approved</label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    name="approved" 
                                    value="1"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    {{ ($user->Approved && ($user->Approved == true || $user->Approved == 1)) ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700">Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="radio" 
                                    name="approved" 
                                    value="0"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    {{ (!$user->Approved || $user->Approved == false || $user->Approved == 0 || $user->Approved == null) ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                   
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 md:pt-6 border-t border-gray-200">
            <button type="button" onclick="history.back()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm order-2 sm:order-1">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm order-1 sm:order-2">
                Update User
            </button>
        </div>
    </form>
</div>

@endsection
@section('page_js')
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    // Add custom CSS for validation errors
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .error {
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
    
    // Initialize form validation
    $("#manageAccountForm").validate({
        ignore: [],
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            user_type: {
                required: true
            },
            security_question: {
                required: true
            },
            security_answer: {
                required: true
            }
        },
        messages: {
            name: {
                required: "Please enter name"
            },
            email: {
                required: "Please enter email",
                email: "Please enter a valid email"
            },
            user_type: {
                required: "Please select user type"
            },
            security_question: {
                required: "Please select security question"
            },
            security_answer: {
                required: "Please enter security answer"
            }
        },
        submitHandler: function(form) {
            // Form submission will be handled by the existing AJAX code
            return false; // Prevent default submission to let AJAX handle it
        }
    });
});
$('#manageAccountForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        var formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        
        $.ajax({
            url: "{{ route('admin.users.update', $user->UserID) }}",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                showNotification('Success!', 'User updated successfully', 'success');
                window.location.href = "{{ route('admin.users.index') }}";
            },
            error: function(xhr, status, error) {
                showNotification('Error!', 'An error occurred while updating user.', 'error');
            }
        });
    }
});
function showNotification(title, message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    // Set background color based on type
    if (type === 'success') {
        notification.className += ' bg-green-500 text-white';
    } else if (type === 'error') {
        notification.className += ' bg-red-500 text-white';
    }
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="font-medium">${title}</p>
                <p class="text-sm">${message}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('translate-x-0');
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endsection
