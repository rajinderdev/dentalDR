@extends('layouts.admin')
@push('styles')


@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Users</h1>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Add New User
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex gap-4 items-center">
        <input type="text" id="searchInput" placeholder="Search users..." class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select id="userTypeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Types</option>
            @foreach($roles as $role)
                <option value="{{ $role->RoleID }}">{{ $role->name }}</option>
            @endforeach
        </select>
        <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="approved">Approved</option>
            <option value="unapproved">Unapproved</option>
            <option value="locked">Locked</option>
            <option value="unlocked">Unlocked</option>
        </select>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="user-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sr No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">User Type</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black/50">
    <div class="relative p-4 w-full max-w-lg">
        <div class="relative bg-white border border-gray-200 rounded-xl shadow-lg p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-5">
                <h3 class="text-lg font-semibold text-gray-800">Change Password</h3>
                <button type="button" onclick="closeChangePasswordModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg w-9 h-9 inline-flex justify-center items-center transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="changePasswordForm">
                @csrf
                <input type="hidden" id="changePasswordUserId" name="user_id">

                <div class="grid grid-cols-1 gap-4">
                    <!-- Old Password -->
                    <div>
                        <label for="old_password" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Old Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="old_password" name="old_password" required
                               placeholder="Enter old password"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block mb-1.5 text-sm font-medium text-gray-700">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="new_password" name="new_password" required minlength="6"
                               placeholder="Enter new password"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                               placeholder="Confirm new password"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeChangePasswordModal()"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                        <i class="fas fa-key mr-1"></i> Change Password
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
    var table = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.users.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
                d.user_type = $('#userTypeFilter').val();
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            {
                data: null,
                name: 'serial',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
           
            { 
                data: 'Name',
                name: 'Name',
                orderable: true
            },
            { 
                data: 'Email',
                name: 'Email',
                orderable: true
            },
            { 
                data: 'UserType',
                name: 'UserType',
                orderable: false
            },
            { 
                data: 'CreatedOn',
                name: 'CreatedOn',
                orderable: true
            },
            // { 
            //     data: 'approved',
            //     name: 'approved',
            //     render: function(data) {
            //         return data ? '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>' : 
            //                '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>';
            //     }
            // },
            // { 
            //     data: 'locked',
            //     name: 'locked',
            //     render: function(data) {
            //         return data ? '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Locked</span>' : 
            //                '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Unlocked</span>';
            //     }
            // },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-right'
            },
        ],
        order: [[4, 'desc']],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No users found',
            info: 'Showing _START_ to _END_ of _TOTAL_ users',
            infoEmpty: 'No users available',
            infoFiltered: '(filtered from _MAX_ total users)',
            lengthMenu: 'Show _MENU_ users',
            search: 'Search:',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        responsive: true
    });

    // Custom search and filter handlers
    $('#searchInput').on('keyup', function() {
        table.draw();
    });

    $('#userTypeFilter').on('change', function() {
        table.draw();
    });

    $('#statusFilter').on('change', function() {
        table.draw();
    });
});

function deleteUser(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.users.destroy", ":userId") }}'.replace(':userId', userId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'User has been deleted.',
                        'success'
                    ).then(() => {
                        // Reload the DataTable
                        $('#user-table').DataTable().ajax.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting user';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire(
                        'Error!',
                        errorMessage,
                        'error'
                    );
                }
            });
        }
    });
}
</script>
<script>
function openChangePasswordModal(userId) {
    $('#changePasswordUserId').val(userId);
    $('#changePasswordModal').removeClass('hidden');
}

function closeChangePasswordModal() {
    $('#changePasswordModal').addClass('hidden');
    $('#changePasswordForm')[0].reset();
}

$(document).ready(function() {
    $('#changePasswordForm').validate({
        rules: {
            old_password: {
                required: true
            },
            new_password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
                required: true,
                minlength: 6,
                equalTo: "#new_password"
            }
        },
        messages: {
            old_password: {
                required: "Please enter old password"
            },
            new_password: {
                required: "Please enter new password",
                minlength: "Password must be at least 6 characters"
            },
            confirm_password: {
                required: "Please confirm password",
                minlength: "Password must be at least 6 characters",
                equalTo: "Passwords do not match"
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url: '{{ route("admin.users.change-password") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: $('#changePasswordUserId').val(),
                    old_password: $('#old_password').val(),
                    new_password: $('#new_password').val(),
                    confirm_password: $('#confirm_password').val()
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            'Password changed successfully.',
                            'success'
                        );
                        closeChangePasswordModal();
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Error changing password.',
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error changing password';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire(
                        'Error!',
                        errorMessage,
                        'error'
                    );
                }
            });
        }
    });
});
</script>
@endsection
