@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Chairs</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.chairs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-1"></i> Add Chair
            </a>
            <button type="button" onclick="bulkDeleteChairs()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-1"></i> Delete Chairs
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" id="searchInput" placeholder="Search chairs..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <a href="{{ route('admin.chairs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- Chairs Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="chair-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                     <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Chair Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Start Time</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">End Time</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Time Interval</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">CreatedOn</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('page_js')
<script>
$(document).ready(function() {
    var table = $('#chair-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.chairs.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
            }
        },
        columns: [
             {
                data: 'ChairID',
                name: 'select',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<input type="checkbox" class="chair-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" value="' + data + '">';
                }
            },
            {
                data: 'Title',
                name: 'Title',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'Description',
                name: 'Description',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'StartTime',
                name: 'StartTime',
                orderable: false
            },
            {
                data: 'EndTime',
                name: 'EndTime',
                orderable: false
            },
            {
                data: 'TimeInterval',
                name: 'TimeInterval',
                orderable: false
            },
           { 
                data: 'CreatedOn',
                name: 'CreatedOn',
                orderable: true
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-right'
            },
        ],
        order: [[6, 'desc']],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No chairs found',
            info: 'Showing _START_ to _END_ of _TOTAL_ chairs',
            infoEmpty: 'No chairs available',
            infoFiltered: '(filtered from _MAX_ total chairs)',
            lengthMenu: 'Show _MENU_ chairs',
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
        $('.chair-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Update select all when individual checkboxes change
    $(document).on('change', '.chair-checkbox', function() {
        if ($('.chair-checkbox:checked').length === $('.chair-checkbox').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    });
});

function deleteChair(chairId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This chair will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.chairs.destroy", ":chairId") }}'.replace(':chairId', chairId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Chair has been deleted.', 'success').then(() => {
                            $('#chair-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting chair.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting chair';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}

function bulkDeleteChairs() {
    var selectedIds = [];
    $('.chair-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        Swal.fire('Warning!', 'Please select at least one chair to delete.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: selectedIds.length + " chair(s) will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.chairs.bulk-delete") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            $('#selectAll').prop('checked', false);
                            $('#chair-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting chairs.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting chairs';
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
