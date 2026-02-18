<div class="flex justify-end space-x-2">
    <button onclick="openChangePasswordModal('{{ $user->UserID }}')" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Change Password
    </button>
    <a href="{{ route('admin.users.edit', $user) }}" 
       class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    
    <button onclick="deleteUser('{{ $user->UserID }}')" 
            class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50"
            title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</div>

