@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage LookUps</h1>
         <div class="flex gap-2">
            <a href="{{ route('admin.lookups.create') }}" id="addNewItemBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-1"></i> Add New Item
            </a>
            <button type="button" id="deleteSelectedBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" disabled>
                <i class="fas fa-trash mr-1"></i> Delete Item
            </button>
        </div>
    </div>

    <!-- Search, Filter and Actions -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" id="searchInput" placeholder="Search item..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
        </select>
      
      
        <a href="{{ route('admin.lookups.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- LookUps Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="lookup-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Title</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Importance</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
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
    var table = $('#lookup-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.lookups.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
                d.category = $('#categoryFilter').val();
            }
        },
        columns: [
            {
                data: 'id',
                name: 'select',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<input type="checkbox" class="row-select w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="' + data + '">';
                }
            },
            {
                data: 'ItemTitle',
                name: 'ItemTitle',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'ItemDescription',
                name: 'ItemDescription',
                orderable: false,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'Importance',
                name: 'Importance',
                orderable: true,
                render: function(data) {
                    return data !== null ? data : '0';
                }
            },
            {
                data: 'ItemCategory',
                name: 'ItemCategory',
                orderable: true
            },
            {
                data: 'LastUpdatedOn',
                name: 'LastUpdatedOn',
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
        order: [[5, 'desc']],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No lookup items found',
            info: 'Showing _START_ to _END_ of _TOTAL_ items',
            infoEmpty: 'No items available',
            infoFiltered: '(filtered from _MAX_ total items)',
            lengthMenu: 'Show _MENU_ items',
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

    $('#categoryFilter').on('change', function() {
        table.draw();
        // Update Add New Item link with selected category
        var category = $(this).val();
        var url = "{{ route('admin.lookups.create') }}";
        if (category) {
            url += '?category=' + encodeURIComponent(category);
        }
        $('#addNewItemBtn').attr('href', url);
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.row-select').prop('checked', $(this).prop('checked'));
        toggleDeleteBtn();
    });

    // Individual checkbox change
    $(document).on('change', '.row-select', function() {
        toggleDeleteBtn();
        if (!$(this).prop('checked')) {
            $('#selectAll').prop('checked', false);
        }
    });

    function toggleDeleteBtn() {
        var checkedCount = $('.row-select:checked').length;
        $('#deleteSelectedBtn').prop('disabled', checkedCount === 0);
    }

    // Bulk delete
    $('#deleteSelectedBtn').on('click', function() {
        var selectedIds = [];
        $('.row-select:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: selectedIds.length + " item(s) will be removed!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.lookups.bulk-delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                $('#selectAll').prop('checked', false);
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message || 'Error deleting items.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error deleting items';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    });
});

function deleteLookup(lookupId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This item will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.lookups.destroy", ":lookupId") }}'.replace(':lookupId', lookupId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Item has been deleted.', 'success').then(() => {
                            $('#lookup-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting item.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting item';
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
