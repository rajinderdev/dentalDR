<div class="flex justify-end space-x-2">
    <a href="{{ route('admin.users.edit', $user) }}" 
       class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50"
       title="View">
        <i class="fas fa-eye"></i>
    </a>
    
    <form action="{{ route('admin.users.destroy', $user) }}" 
          method="POST" 
          class="inline"
          onsubmit="return confirm('Are you sure you want to delete this user?');">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50"
                title="Delete">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
