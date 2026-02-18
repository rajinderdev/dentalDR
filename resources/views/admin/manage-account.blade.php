@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manage Account</h1>
    
    <form id="manageAccountForm" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Column --}}
            <div class="space-y-4">
                {{-- Login ID --}}
                <div>
                    <label for="login_id" class="block text-sm font-medium text-gray-700 mb-2">Login ID</label>
                    <input 
                        type="text" 
                        id="login_id" 
                        name="login_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter login ID"
                        value="{{ $user->login_id ?? '' }}"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter password"
                    >
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter email address"
                        value="{{ $user->email ?? '' }}"
                    >
                </div>

                {{-- Security Answer --}}
                <div>
                    <label for="security_answer" class="block text-sm font-medium text-gray-700 mb-2">Security Answer</label>
                    <input 
                        type="text" 
                        id="security_answer" 
                        name="security_answer"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter security answer"
                        value="{{ $user->security_answer ?? '' }}"
                    >
                </div>

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
                                {{ ($user->approved ?? 0) == 1 ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-sm text-gray-700">Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="approved" 
                                value="0"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                {{ ($user->approved ?? 0) == 0 ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-4">
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
                                {{ ($user->locked ?? 0) == 1 ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-sm text-gray-700">Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="locked" 
                                value="0"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                {{ ($user->locked ?? 0) == 0 ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Confirm password"
                    >
                </div>

                {{-- Security Question --}}
                <div>
                    <label for="security_question" class="block text-sm font-medium text-gray-700 mb-2">Security Question</label>
                    <select 
                        id="security_question" 
                        name="security_question"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Select a security question</option>
                        <option value="pet" {{ ($user->security_question ?? '') == 'pet' ? 'selected' : '' }}>What was your first pet's name?</option>
                        <option value="school" {{ ($user->security_question ?? '') == 'school' ? 'selected' : '' }}>What elementary school did you attend?</option>
                        <option value="city" {{ ($user->security_question ?? '') == 'city' ? 'selected' : '' }}>In what city were you born?</option>
                        <option value="mother_maiden" {{ ($user->security_question ?? '') == 'mother_maiden' ? 'selected' : '' }}>What is your mother's maiden name?</option>
                        <option value="first_car" {{ ($user->security_question ?? '') == 'first_car' ? 'selected' : '' }}>What was your first car?</option>
                    </select>
                </div>

                {{-- User Type --}}
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-700 mb-2">User Type</label>
                    <select 
                        id="user_type" 
                        name="user_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Select user type</option>
                        <option value="admin" {{ ($user->user_type ?? '') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="doctor" {{ ($user->user_type ?? '') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                        <option value="nurse" {{ ($user->user_type ?? '') == 'nurse' ? 'selected' : '' }}>Nurse</option>
                        <option value="receptionist" {{ ($user->user_type ?? '') == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                        <option value="lab_staff" {{ ($user->user_type ?? '') == 'lab_staff' ? 'selected' : '' }}>Lab Staff</option>
                        <option value="accountant" {{ ($user->user_type ?? '') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
            <button 
                type="button" 
                onclick="window.history.back()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
                Cancel
            </button>
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            >
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('manageAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Basic validation
    if (data.password && data.password !== data.password_confirmation) {
        alert('Passwords do not match!');
        return;
    }
    
    if (!data.email) {
        alert('Email is required!');
        return;
    }
    
    // Submit via AJAX
    fetch('{{ route("admin.users.update", $user->id ?? 'new') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Account updated successfully!');
            // Redirect or reload as needed
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the account.');
    });
});
</script>
@endsection
