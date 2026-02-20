<div class="flex justify-end space-x-2">
    <button onclick="editBankAccount('{{ $account->BankAccountID }}')"
            class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50"
            title="Edit">
        <i class="fas fa-edit"></i>
    </button>
    <button onclick="deleteBankAccount('{{ $account->BankAccountID }}')"
            class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50"
            title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</div>
