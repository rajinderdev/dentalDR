<div class="flex justify-end space-x-2">
    <button onclick="editDocType('{{ $docType->FolderId }}')"
            class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50"
            title="Edit">
        <i class="fas fa-edit"></i>
    </button>
    <button onclick="deleteDocType('{{ $docType->FolderId }}')"
            class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50"
            title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</div>
