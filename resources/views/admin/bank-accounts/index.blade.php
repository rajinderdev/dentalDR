@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Bank Accounts</h1>
        <div class="flex gap-2">
            <button type="button" onclick="openAddModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-1"></i> Add Bank Account
            </button>
            <button type="button" onclick="bulkDeleteAccounts()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-1"></i> Delete Bank Account
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <label for="searchInput" class="text-sm font-medium text-gray-700">Search in Bank Accounts:</label>
        <input type="text" id="searchInput" placeholder="Search..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
        <a href="{{ route('admin.bank-accounts.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- Bank Accounts Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="bank-account-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Bank Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Account Number</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Branch</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">City</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Bank Account Modal -->
<div id="bankAccountModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg relative">
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                <h2 id="modalTitle" class="text-lg font-semibold text-gray-800">Bank Accounts</h2>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="bankAccountForm" class="px-6 py-4">
                @csrf
                <input type="hidden" id="editAccountId" value="">

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 border-b border-gray-200 pb-2">Bank Accounts</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <label for="BankAccountName" class="w-32 text-sm font-medium text-gray-700 text-right">Bank Name:</label>
                            <input type="text" id="BankAccountName" name="BankAccountName"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                   placeholder="Enter bank name" required>
                        </div>
                        <div class="flex items-center gap-4">
                            <label for="AccountNumber" class="w-32 text-sm font-medium text-gray-700 text-right">Account Number:</label>
                            <input type="text" id="AccountNumber" name="AccountNumber"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                   placeholder="Enter account number">
                        </div>
                        <div class="flex items-center gap-4">
                            <label for="Branch" class="w-32 text-sm font-medium text-gray-700 text-right">Branch:</label>
                            <input type="text" id="Branch" name="Branch"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                   placeholder="Enter branch">
                        </div>
                        <div class="flex items-center gap-4">
                            <label for="City" class="w-32 text-sm font-medium text-gray-700 text-right">City:</label>
                            <input type="text" id="City" name="City"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                   placeholder="Enter city">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-200">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-sm">
                        Save
                    </button>
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script>
$(document).ready(function() {
    var table = $('#bank-account-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.bank-accounts.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
            }
        },
        columns: [
            {
                data: 'BankAccountID',
                name: 'select',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<input type="checkbox" class="account-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" value="' + data + '">';
                }
            },
            {
                data: 'BankAccountName',
                name: 'BankAccountName',
                orderable: true,
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'AccountNumber',
                name: 'AccountNumber',
                orderable: true,
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'Branch',
                name: 'Branch',
                orderable: true,
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'City',
                name: 'City',
                orderable: true,
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-right'
            },
        ],
        order: [[1, 'asc']],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No bank accounts found',
            info: 'Showing _START_ to _END_ of _TOTAL_ bank accounts',
            infoEmpty: 'No bank accounts available',
            infoFiltered: '(filtered from _MAX_ total bank accounts)',
            lengthMenu: 'Records: _MENU_',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        responsive: true
    });

    // Custom search handler
    $('#searchInput').on('keyup', function() {
        table.draw();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.account-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Update select all when individual checkboxes change
    $(document).on('change', '.account-checkbox', function() {
        if ($('.account-checkbox:checked').length === $('.account-checkbox').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    });
});

function openAddModal() {
    $('#modalTitle').text('Bank Accounts');
    $('#editAccountId').val('');
    $('#bankAccountForm')[0].reset();
    $('#bankAccountModal').removeClass('hidden');
}

function closeModal() {
    $('#bankAccountModal').addClass('hidden');
    $('#bankAccountForm')[0].reset();
    $('#editAccountId').val('');
}

function editBankAccount(accountId) {
    $.ajax({
        url: '{{ route("admin.bank-accounts.edit", ":id") }}'.replace(':id', accountId),
        type: 'GET',
        success: function(data) {
            $('#modalTitle').text('Bank Accounts');
            $('#editAccountId').val(data.BankAccountID);
            $('#BankAccountName').val(data.BankAccountName);
            $('#AccountNumber').val(data.AccountNumber);
            $('#Branch').val(data.Branch);
            $('#City').val(data.City);
            $('#bankAccountModal').removeClass('hidden');
        },
        error: function(xhr) {
            Swal.fire('Error!', 'Failed to load bank account details.', 'error');
        }
    });
}

$('#bankAccountForm').on('submit', function(e) {
    e.preventDefault();

    var accountId = $('#editAccountId').val();
    var isEdit = accountId !== '';
    var url = isEdit
        ? '{{ route("admin.bank-accounts.update", ":id") }}'.replace(':id', accountId)
        : '{{ route("admin.bank-accounts.store") }}';

    var formData = {
        _token: '{{ csrf_token() }}',
        BankAccountName: $('#BankAccountName').val(),
        AccountNumber: $('#AccountNumber').val(),
        Branch: $('#Branch').val(),
        City: $('#City').val()
    };

    if (isEdit) {
        formData._method = 'PUT';
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function(res) {
            if (res.success) {
                closeModal();
                Swal.fire('Success!', res.message, 'success').then(() => {
                    $('#bank-account-table').DataTable().ajax.reload();
                });
            } else {
                Swal.fire('Error!', res.message || 'An error occurred.', 'error');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
            }
            Swal.fire('Error!', errorMessage, 'error');
        }
    });
});

function deleteBankAccount(accountId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This bank account will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.bank-accounts.destroy", ":id") }}'.replace(':id', accountId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Bank account has been deleted.', 'success').then(() => {
                            $('#bank-account-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting bank account.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting bank account';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}

function bulkDeleteAccounts() {
    var selectedIds = [];
    $('.account-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        Swal.fire('Warning!', 'Please select at least one bank account to delete.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: selectedIds.length + " bank account(s) will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.bank-accounts.bulk-delete") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            $('#selectAll').prop('checked', false);
                            $('#bank-account-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting bank accounts.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting bank accounts';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}
</script>
@endsection
