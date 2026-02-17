@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manage Roles</h1>
    
    <form id="rolePermissionsForm">
        @csrf
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">
                            Role Name
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                            Module Accessibility
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900 border-r border-gray-200">
                                {{ $role->name }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($modules as $module)
                                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded">
                                            <input 
                                                type="checkbox" 
                                                name="role_permissions[{{ $role->name }}][]"
                                                value="{{ $module->ModuleCode }}"
                                                {{ in_array($module->ModuleCode, $rolePermissions[$role->name] ?? []) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            >
                                            <span class="text-sm text-gray-700">{{ $module->ModuleName }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            >
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('rolePermissionsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Show loading state
    submitButton.disabled = true;
    submitButton.textContent = 'Saving...';
    
    // Convert FormData to JSON
    const rolePermissions = {};
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('role_permissions[')) {
            const match = key.match(/role_permissions\[(.*?)\]\[\]/);
            if (match) {
                const roleName = match[1];
                if (!rolePermissions[roleName]) {
                    rolePermissions[roleName] = [];
                }
                rolePermissions[roleName].push(value);
            }
        }
    }
    
    fetch('{{ route("admin.roles.update") }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            role_permissions: rolePermissions
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Success!', data.message, 'success');
        } else {
            // Show error message
            showNotification('Error!', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error!', 'An unexpected error occurred', 'error');
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
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
