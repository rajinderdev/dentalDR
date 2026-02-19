<div class="flex justify-end space-x-2">
    <a href="{{ route('admin.medicines.edit', $medicine->DrugId) }}"
       class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    <button onclick="deleteMedicine('{{ $medicine->DrugId }}')"
            class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50"
            title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</div>
