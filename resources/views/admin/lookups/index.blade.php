@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage LookUps</h1>
         <div class="flex gap-2">
            <button type="button" onclick="openAddLookupModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-1"></i> Add New LookUp
            </button>
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

<!-- Add Lookup Modal -->
<div id="lookupModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black/50">
    <div class="relative p-4 w-full max-w-lg">
        <div class="relative bg-white border border-gray-200 rounded-xl shadow-lg p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-5">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Item</h3>
                <button type="button" onclick="closeLookupModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg w-9 h-9 inline-flex justify-center items-center transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="lookupForm">
                @csrf
                <input type="hidden" name="editLookupId" id="editLookupId" value="">

                <div class="grid grid-cols-1 gap-4">
                    <!-- Category -->
                    <div>
                        <label for="ItemCategory" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="ItemCategory" name="ItemCategory"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="ItemTitle" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="ItemTitle" name="ItemTitle" value=""
                               placeholder="Enter item title"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" required />
                    </div>
                <!-- Importance -->
                    <div>
                        <label for="Importance" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Importance
                        </label>
                        <input type="number" id="Importance" name="Importance" min="0" value="0"
                               placeholder="0"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                    </div>
                    <!-- Description -->
                    <div>
                        <label for="ItemDescription" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea id="ItemDescription" name="ItemDescription" rows="3"
                                  placeholder="Enter item description"
                                  class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400"></textarea>
                    </div>

                   
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeLookupModal()"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                        <i class="fas fa-save mr-1"></i> Save Item
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
                className: 'text-right',
               
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
        text: "This look up will be removed from the system!",
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
                        Swal.fire('Deleted!', 'Look up has been deleted.', 'success').then(() => {
                            $('#lookup-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting look up.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting look up';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}
$(document).ready(function() {
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            label.error {
                color: #dc3545 !important;
                font-size: 0.875rem !important;
                margin-top: 0.25rem !important;
                display: block !important;
            }
            input.error, select.error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
            }
        `)
        .appendTo('head');

    $("#lookupForm").validate({
        ignore: [],
        rules: {
            ItemTitle: { required: true },
            ItemCategory: { required: true }
        },
        messages: {
            ItemTitle: { required: "Please enter Look up title" },
            ItemCategory: { required: "Please select a category" }
        },
        submitHandler: function(form) {
            return false;
        }
    });
});
// Modal functions
function openAddLookupModal() {
    $('#modalTitle').text('Add New LookUps');
    $('#editLookupId').val('');
    $('#lookupForm')[0].reset();
    $('#lookupModal').removeClass('hidden').addClass('flex');
    
    // Set category from filter if selected
    var category = $('#categoryFilter').val();
    if (category) {
        $('#ItemCategory').val(category);
    }
}

function closeLookupModal() {
    $('#lookupModal').addClass('hidden').removeClass('flex');
    $('#lookupForm')[0].reset();
    $('#editLookupId').val('');
    $('#lookupForm').data('validator').resetForm();
}

function editLookup(lookupId) {
    $.ajax({
        url: '{{ route("admin.lookups.edit", ":id") }}'.replace(':id', lookupId),
        type: 'GET',
        success: function(data) {
            $('#modalTitle').text('Edit Item');
            $('#editLookupId').val(data.data.id);
            $('#ItemCategory').val(data.data.ItemCategory);
            $('#ItemTitle').val(data.data.ItemTitle);
            $('#ItemDescription').val(data.data.ItemDescription);
            $('#Importance').val(data.data.Importance);
            $('#lookupModal').removeClass('hidden').addClass('flex');
        },
        error: function(xhr) {
            Swal.fire('Error!', 'Failed to load look up details.', 'error');
        }
    });
}

// Form submission
$('#lookupForm').on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
    
        var formData = new FormData(this);
        var lookupId = $('#editLookupId').val();
        var url = lookupId ? 
            '{{ route("admin.lookups.update", ":id") }}'.replace(':id', lookupId) : 
            '{{ route("admin.lookups.store") }}';
        var method = lookupId ? 'POST' : 'POST';
        
        if (lookupId) {
            formData.append('_method', 'PUT');
        }
    
        $.ajax({
            url: url,
            type: method,
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success').then(() => {
                        closeLookupModal();
                        $('#lookup-table').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message || 'An error occurred.', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while saving look up.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                }
                Swal.fire('Error!', errorMessage, 'error');
            }
        });
    }
});
</script>
@endsection
