@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
    
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 md:px-4 py-2 mb-4 md:mb-6 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm md:text-base">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Users
    </a>
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Create Account</h1>
    <form id="createUserForm" class="space-y-4 md:space-y-6">
         @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
            {{-- Left Column --}}
            <div class="space-y-3 md:space-y-4">
                {{-- Login ID --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name<sup class="star">*</sup></label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter Name"
                        required
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password<sup class="star">*</sup></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter password"
                        minlength="6"
                        required
                    > 
                </div>
                
                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password<sup class="star">*</sup></label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Confirm password"
                        minlength="6"
                        required
                    > 
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email<sup class="star">*</sup></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        placeholder="Enter email address"
                        required
                    >
                </div>

                
            </div>

            {{-- Right Column --}}
            <div class="space-y-3 md:space-y-4">
                {{-- Security Question --}}
                <div>
                    <label for="security_question" class="block text-sm font-medium text-gray-700 mb-2">Security Question<sup class="star">*</sup></label>
                    <select 
                        id="security_question" 
                        name="security_question"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        required
                    >
                        <option value="">Select a security question</option>
                        <option value="pet">What was your first pet's name?</option>
                        <option value="school">What elementary school did you attend?</option>
                        <option value="city">In what city were you born?</option>
                        <option value="mother_maiden">What is your mother's maiden name?</option>
                        <option value="first_car">What was your first car?</option>
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
                        required
                    >
                </div>

                {{-- Role Selection --}}
                <div>
                    <label for="RoleID" class="block text-sm font-medium text-gray-700 mb-2"> User Type<sup class="star">*</sup></label>
                    <select 
                        id="RoleID" 
                        name="RoleID"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        required
                    >
                        <option value="">Select user type</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->RoleID }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 md:pt-6 border-t border-gray-200">
            <button 
                type="button" 
                onclick="window.history.back()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm order-2 sm:order-1"
            >
                Cancel
            </button>
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-sm order-1 sm:order-2"
            >
                Create Account
            </button>
        </div>
    </form>
</div>
@endsection
@section('page_js')


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
    $("#createUserForm").validate({
        ignore: [],
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 6
            },
            password_confirmation: {
                required: true,
                equalTo: "#password"
            },
            RoleID: {
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
            password: {
                required: "Please enter password",
                minlength: "Password must be at least 6 characters"
            },
            password_confirmation: {
                required: "Please confirm password",
                equalTo: "Passwords do not match"
            },
            RoleID: {
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
$('#createUserForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        $.ajax({
            url: "{{ route('admin.users.store') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                showNotification('Success!', 'User created successfully', 'success');
                window.location.href = "{{ route('admin.users.index') }}";
            },
            error: function(xhr, status, error) {
                showNotification('Error!', 'An error occurred while creating user.', 'error');
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
