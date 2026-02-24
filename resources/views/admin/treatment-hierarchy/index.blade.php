@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Treatment Hierarchy</h1>
        <div class="flex gap-2">
            <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                Add Treatment Type
            </button>
            <button type="button" onclick="bulkDeleteTreatments()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                Delete Treatment Type
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <label for="searchInput" class="text-sm font-medium text-gray-700">Search Treatment:</label>
        <input type="text" id="searchInput" placeholder="Search..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
        <a href="{{ route('admin.treatment-hierarchy.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
            Clear Filters
        </a>
    </div>

    <!-- Treatment Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="treatment-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Treatment Type</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">General Treatment Cost</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Specialist Treatment Cost</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Doctor Incentive</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Steps Count</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Created On</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Treatment Modal -->
<div id="treatmentModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black/50">
    <div class="relative p-4 w-full max-w-lg">
        <div class="relative bg-white border border-gray-200 rounded-xl shadow-lg p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-5">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add Treatment Type</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg w-9 h-9 inline-flex justify-center items-center transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="treatmentForm">
                @csrf
                <input type="hidden" id="editTreatmentId" value="">

                <div class="grid grid-cols-1 gap-4">
                    <!-- Parent Treatment Type -->
                    <div>
                        <label for="ParentTreatmentTypeID" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Parent Treatment Type
                        </label>
                        <select id="ParentTreatmentTypeID" name="ParentTreatmentTypeID"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="00000000-0000-0000-0000-000000000000"> Select Parent Treatment Type</option>
                            @foreach ($treatmentTypes as $treatmentType)
                                <option value="{{ $treatmentType->TreatmentTypeID }}">{{ $treatmentType->Title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Treatment Name -->
                    <div>
                        <label for="Title" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Treatment Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="Title" name="Title"
                               placeholder="Enter treatment name"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" required />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="Description" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea id="Description" name="Description" rows="3"
                                  placeholder="Enter description"
                                  class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400"></textarea>
                    </div>

                    <!-- General Cost -->
                    <div>
                        <label for="GeneralTreatmentCost" class="block mb-1.5 text-sm font-medium text-gray-700">
                            General Cost
                        </label>
                        <input type="number" id="GeneralTreatmentCost" name="GeneralTreatmentCost" step="0.01" min="0" value="0"
                               placeholder="0.00"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                    </div>

                    <!-- Specialist Cost -->
                    <div>
                        <label for="SpecialistTreatmentCost" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Specialist Cost
                        </label>
                        <input type="number" id="SpecialistTreatmentCost" name="SpecialistTreatmentCost" step="0.01" min="0" value="0"
                               placeholder="0.00"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                    </div>
                    <!-- Doctor Incentive Amount -->
                    <div>
                        <label for="DoctorIncentiveAmount" class="block mb-1.5 text-sm font-medium text-gray-700">
                            Doctor Incentive Amount
                        </label>
                        <input type="number" id="DoctorIncentiveAmount" name="DoctorIncentiveAmount" step="0.01" min="0" value="0"
                               placeholder="0.00"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                        <i class="fas fa-save mr-1"></i> Save Treatment Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Steps Modal (View/Add Steps for a Treatment) -->
<div id="stepsModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black/50">
    <div class="relative p-4 w-full max-w-6xl">
        <div class="relative bg-white border border-gray-200 rounded-xl shadow-lg p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-5">
                <h3 class="text-lg font-semibold text-gray-800" id="stepsModalTitle">Treatment Hierarchy</h3>
                <button type="button" onclick="closeStepsModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg w-9 h-9 inline-flex justify-center items-center transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="stepsForm">
                @csrf
                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-700 mb-1 block">Parent Treatment Type</label>
                        <div id="parentTreatmentName" class="px-3 py-2.5 bg-gray-100 rounded-lg text-sm font-medium text-gray-800 mt-1"></div>
                        <input type="hidden" id="parentTreatmentId3" name="parentTreatmentId" value="">
                    </div>

                    <!-- Existing Steps List -->
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Current Steps</h3>
                        <div id="stepsList" class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                            <div class="p-3 text-sm text-gray-500 text-center">Loading...</div>
                        </div>
                    </div>

                    <!-- Add Step Section -->
                    <div class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Treatment Name -->
                            <div>
                                <label for="Title" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    Treatment Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="Title1" name="Title"
                                    placeholder="Enter treatment name"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" required />
                            </div>


                            <!-- General Cost -->
                            <div>
                                <label for="GeneralTreatmentCost" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    General Cost
                                </label>
                                <input type="number" id="GeneralTreatmentCost2" name="GeneralTreatmentCost" step="0.01" min="0" value="0"
                                    placeholder="0.00"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                            </div>

                            <!-- Specialist Cost -->
                            <div>
                                <label for="SpecialistTreatmentCost" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    Specialist Cost
                                </label>
                                <input type="number" id="SpecialistTreatmentCost3" name="SpecialistTreatmentCost" step="0.01" min="0" value="0"
                                    placeholder="0.00"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                            </div>

                            <!-- Doctor Incentive Amount -->
                            <div>
                                <label for="DoctorIncentiveAmount" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    Doctor Incentive Amount
                                </label>
                                <input type="number" id="DoctorIncentiveAmount4" name="DoctorIncentiveAmount" step="0.01" min="0" value="0"
                                    placeholder="0.00"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400" />
                            </div>
                            
                            <!-- Description -->
                            <div>
                                <label for="Description" class="block mb-1.5 text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea id="Description5" name="Description" rows="3"
                                        placeholder="Enter description"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-gray-400"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
            
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeStepsModal()"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                        <i class="fas fa-save mr-1"></i> Save Treatment Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('page_js')
<script>
var treatmentTable;
$(document).ready(function() {
    treatmentTable = $('#treatment-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.treatment-hierarchy.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
            }
        },
        columns: [
            {
                data: 'TreatmentTypeID',
                name: 'select',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<input type="checkbox" class="treatment-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" value="' + data + '">';
                }
            },
            { data: 'Title', name: 'Title', orderable: true },
            {
                data: 'GeneralTreatmentCost',
                name: 'GeneralTreatmentCost',
                orderable: true,
                className: 'text-right',
                render: function(data) {
                    return 'INR ' + data;
                }
            },
            {
                data: 'SpecialistTreatmentCost',
                name: 'SpecialistTreatmentCost',
                orderable: true,
                className: 'text-right',
                render: function(data) {
                    return 'INR ' + data;
                }
            },
            {
                data: 'DoctorIncentiveAmount',
                name: 'DoctorIncentiveAmount',
                orderable: true,
                className: 'text-right',
                render: function(data) {
                    return 'INR ' + data;
                }
            },
            {
                data: 'StepsCount',
                name: 'StepsCount',
                orderable: false,
                className: 'text-center'
            },
            {
                data: 'CreatedOn',
                name: 'CreatedOn',
                orderable: true,
                className: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
        ],
        order: [[6, 'DESC']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No treatment types found',
            info: 'Showing _START_ to _END_ of _TOTAL_ treatment types',
            infoEmpty: 'No treatment types available',
            infoFiltered: '(filtered from _MAX_ total)',
            lengthMenu: 'Records: _MENU_',
            paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
        },
        responsive: true
    });

    $('#searchInput').on('keyup', function() { treatmentTable.draw(); });

    $('#selectAll').on('change', function() {
        $('.treatment-checkbox').prop('checked', $(this).is(':checked'));
    });
    $(document).on('change', '.treatment-checkbox', function() {
        $('#selectAll').prop('checked', $('.treatment-checkbox:checked').length === $('.treatment-checkbox').length);
    });

    // Form validation
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

    $("#treatmentForm").validate({
        ignore: [],
        rules: { Title: { required: true } },
        messages: { Title: { required: "Please enter treatment name" } },
        submitHandler: function(form) { return false; }
    });
    $("#stepsForm").validate({
        ignore: [],
        rules: { Title: { required: true } },
        messages: { Title: { required: "Please enter treatment name" } },
        submitHandler: function(form) { return false; }
    });
});

// ========== Create/Edit Treatment ==========
function openCreateModal() {
    $('#modalTitle').text('Create Treatment Type');
    $('#editTreatmentId').val('');
    $('#treatmentForm')[0].reset();
    $('#GeneralTreatmentCost').val(0);
    $('#SpecialistTreatmentCost').val(0);
    
    $('#treatmentModal').removeClass('hidden').addClass('flex');
}



function closeModal() {
    $('#treatmentModal').addClass('hidden');
    $('#treatmentForm')[0].reset();
    $('#editTreatmentId').val('');
}

function editTreatment(treatmentId) {
    // Load parent treatment options first
    
    $.ajax({
        url: '{{ route("admin.treatment-hierarchy.edit", ":id") }}'.replace(':id', treatmentId),
        type: 'GET',
        success: function(data) {
            $('#modalTitle').text('Edit Treatment Type');
            $('#editTreatmentId').val(data.TreatmentTypeID);
            $('#Title').val(data.Title);
            $('#Description').val(data.Description);
            $('#GeneralTreatmentCost').val(data.GeneralTreatmentCost || 0);
            $('#SpecialistTreatmentCost').val(data.SpecialistTreatmentCost || 0);
            
            // Set parent treatment type after loading options
            setTimeout(function() {
                $('#ParentTreatmentTypeID').val(data.ParentTreatmentTypeID || '');
            }, 100);
            
            $('#treatmentModal').removeClass('hidden').addClass('flex');
        },
        error: function() {
            Swal.fire('Error!', 'Failed to load treatment details.', 'error');
        }
    });
}

$('#treatmentForm').on('submit', function(e) {
    e.preventDefault();
    if (!$('#treatmentForm').valid()) return;

    var treatmentId = $('#editTreatmentId').val();
    var isEdit = treatmentId !== '';
    var url = isEdit
        ? '{{ route("admin.treatment-hierarchy.update", ":id") }}'.replace(':id', treatmentId)
        : '{{ route("admin.treatment-hierarchy.store") }}';

    var formData = {
        _token: '{{ csrf_token() }}',
        Title: $('#Title').val(),
        Description: $('#Description').val(),
        ParentTreatmentTypeID: $('#ParentTreatmentTypeID').val() || '00000000-0000-0000-0000-000000000000',
        GeneralTreatmentCost: $('#GeneralTreatmentCost').val(),
        SpecialistTreatmentCost: $('#SpecialistTreatmentCost').val(),
        DoctorIncentiveAmount: $('#DoctorIncentiveAmount').val()
    };
    if (isEdit) formData._method = 'PUT';

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function(res) {
            if (res.success) {
                closeModal();
                Swal.fire('Success!', res.message, 'success').then(function() {
                    treatmentTable.ajax.reload();
                });
            } else {
                Swal.fire('Error!', res.message || 'An error occurred.', 'error');
            }
        },
        error: function(xhr) {
            var msg = 'An error occurred.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            Swal.fire('Error!', msg, 'error');
        }
    });
});
$('#stepsForm').on('submit', function(e) {
    e.preventDefault();
    if (!$('#stepsForm').valid()) return;

    var treatmentId = $('#editTreatmentId').val();
    var isEdit = treatmentId !== '';
    var url = isEdit
        ? '{{ route("admin.treatment-hierarchy.update", ":id") }}'.replace(':id', treatmentId)
        : '{{ route("admin.treatment-hierarchy.store") }}';

    var formData = {
        _token: '{{ csrf_token() }}',
        Title: $('#Title1').val(),
        Description: $('#Description5').val(),
        ParentTreatmentTypeID: $('#parentTreatmentId3').val() || '00000000-0000-0000-0000-000000000000',
        GeneralTreatmentCost: $('#GeneralTreatmentCost2').val(),
        SpecialistTreatmentCost: $('#SpecialistTreatmentCost3').val(),
        DoctorIncentiveAmount: $('#DoctorIncentiveAmount4').val()
    };
    if (isEdit) formData._method = 'PUT';

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function(res) {
            if (res.success) {
                closeStepsModal();
                Swal.fire('Success!', res.message, 'success').then(function() {
                    treatmentTable.ajax.reload();
                });
            } else {
                Swal.fire('Error!', res.message || 'An error occurred.', 'error');
            }
        },
        error: function(xhr) {
            var msg = 'An error occurred.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            Swal.fire('Error!', msg, 'error');
        }
    });
});

// ========== Delete ==========
function deleteTreatment(treatmentId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This treatment type and its steps will be removed!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.treatment-hierarchy.destroy", ":id") }}'.replace(':id', treatmentId),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success').then(function() {
                            treatmentTable.ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Error deleting treatment.', 'error');
                }
            });
        }
    });
}

function bulkDeleteTreatments() {
    var selectedIds = [];
    $('.treatment-checkbox:checked').each(function() { selectedIds.push($(this).val()); });

    if (selectedIds.length === 0) {
        Swal.fire('Warning!', 'Please select at least one treatment type.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: selectedIds.length + " treatment type(s) will be removed!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete them!'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.treatment-hierarchy.bulk-delete") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', ids: selectedIds },
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success').then(function() {
                            $('#selectAll').prop('checked', false);
                            treatmentTable.ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Error deleting treatments.', 'error');
                }
            });
        }
    });
}

// ========== Steps Modal ==========
function viewSteps(treatmentId, treatmentTitle) {
    $('#parentTreatmentId3').val(treatmentId);
    $('#parentTreatmentName').text(treatmentTitle);
    $('#stepsModalTitle').text('Treatment Hierarchy');
    $('#customStepName').val('');
    $('#masterSearchInput').val('');
    $('#masterTreatmentsList').addClass('hidden');
    loadSteps(treatmentId);
    $('#stepsModal').removeClass('hidden');
}

function closeStepsModal() {
    $('#stepsModal').addClass('hidden');
    treatmentTable.ajax.reload();
}

function loadSteps(treatmentId) {
    $('#stepsList').html('<div class="p-3 text-sm text-gray-500 text-center">Loading...</div>');
    $.ajax({
        url: '{{ route("admin.treatment-hierarchy.steps", ":id") }}'.replace(':id', treatmentId),
        type: 'GET',
        success: function(steps) {
            if (steps.length === 0) {
                $('#stepsList').html('<div class="p-3 text-sm text-gray-500 text-center">No steps added yet</div>');
                return;
            }
            var html = `<div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr class="bg-gray-50 border-b border-gray-200"> <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sr No.</th>
                          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Treatment Type</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">General Treatment Cost</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Specialist Treatment Cost</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Doctor Incentive</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 w-20">Action</th></tr></thead><tbody>`;
            steps.forEach(function(step, index) {
                html += '<tr class="border-b border-gray-100 hover:bg-gray-50">';
                html += '<td class="px-4 py-3 text-sm text-gray-700">' + (index + 1) + '</td>';
                html += '<td class="px-4 py-3 text-sm text-gray-700">' + $('<div>').text(step.Title).html() + '</td>';
                html += '<td class="px-4 py-3 text-right text-sm text-gray-700">' + (step.GeneralTreatmentCost ? 'INR ' + parseFloat(step.GeneralTreatmentCost).toFixed(2) : 'INR 0.00') + '</td>';
                html += '<td class="px-4 py-3 text-right text-sm text-gray-700">' + (step.SpecialistTreatmentCost ? 'INR ' + parseFloat(step.SpecialistTreatmentCost).toFixed(2) : 'INR 0.00') + '</td>';
                html += '<td class="px-4 py-3 text-right text-sm text-gray-700">' + (step.DoctorIncentiveAmount ? 'INR ' + parseFloat(step.DoctorIncentiveAmount).toFixed(2) : 'INR 0.00') + '</td>';
                html += '<td class="px-4 py-3 text-right"><button type="button" onclick="removeStep(\'' + step.TreatmentTypeID + '\')" class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded"><i class="fas fa-trash"></i></button></td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
            html += '</tbody></table>';
            $('#stepsList').html(html);
        },
        error: function() {
            $('#stepsList').html('<div class="p-3 text-sm text-red-500 text-center">Error loading steps</div>');
        }
    });
}

function removeStep(stepId) {
    Swal.fire({
        title: 'Remove step?',
        text: "This step will be removed from the treatment.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.treatment-hierarchy.remove-step", ":id") }}'.replace(':id', stepId),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        loadSteps($('#parentTreatmentId').val());
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                }
            });
        }
    });
}

function addCustomStep() {
    var stepName = $('#customStepName').val().trim();
    if (!stepName) {
        Swal.fire('Warning!', 'Please enter a step name.', 'warning');
        return;
    }

    $.ajax({
        url: '{{ route("admin.treatment-hierarchy.add-step") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            ParentTreatmentTypeID: $('#parentTreatmentId').val(),
            Title: stepName,
            Description: stepName
        },
        success: function(res) {
            if (res.success) {
                $('#customStepName').val('');
                loadSteps($('#parentTreatmentId').val());
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error!', xhr.responseJSON?.message || 'Error adding step.', 'error');
        }
    });
}

// Search master treatments for steps
var masterSearchTimer;
$('#masterSearchInput').on('keyup', function() {
    clearTimeout(masterSearchTimer);
    var q = $(this).val().trim();
    if (q.length < 2) {
        $('#masterTreatmentsList').addClass('hidden');
        return;
    }
    masterSearchTimer = setTimeout(function() {
        $.ajax({
            url: '{{ route("admin.treatment-hierarchy.search-master") }}',
            type: 'GET',
            data: { q: q },
            success: function(treatments) {
                if (treatments.length === 0) {
                    $('#masterTreatmentsList').html('<div class="p-3 text-sm text-gray-500 text-center">No treatments found</div>');
                } else {
                    var html = '';
                    treatments.forEach(function(t) {
                        html += '<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 text-sm" onclick="addMasterStepToParent(\'' + t.TreatmentTypeID + '\', \'' + $('<div>').text(t.Title).html().replace(/'/g, "\\'") + '\')">';
                        html += '<span class="text-gray-800">' + $('<div>').text(t.Title).html() + '</span>';
                        html += '</div>';
                    });
                    $('#masterTreatmentsList').html(html);
                }
                $('#masterTreatmentsList').removeClass('hidden');
            }
        });
    }, 300);
});

function addMasterStepToParent(masterTreatmentId, title) {
    $.ajax({
        url: '{{ route("admin.treatment-hierarchy.add-from-master") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            ParentTreatmentTypeID: $('#parentTreatmentId').val(),
            MasterTreatmentTypeID: masterTreatmentId
        },
        success: function(res) {
            if (res.success) {
                loadSteps($('#parentTreatmentId').val());
                $('#masterSearchInput').val('');
                $('#masterTreatmentsList').addClass('hidden');
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error!', xhr.responseJSON?.message || 'Error adding treatment.', 'error');
        }
    });
}

// ========== Add Treatment Type from Master (top-level) ==========
function openAddFromMasterModal() {
    $('#topMasterSearchInput').val('');
    $('#topMasterTreatmentsList').html('<div class="p-3 text-sm text-gray-500 text-center">Type to search master treatments...</div>');
    $('#addFromMasterTopModal').removeClass('hidden');
}

function closeAddFromMasterModal() {
    $('#addFromMasterTopModal').addClass('hidden');
}

var topMasterSearchTimer;
$('#topMasterSearchInput').on('keyup', function() {
    clearTimeout(topMasterSearchTimer);
    var q = $(this).val().trim();
    if (q.length < 2) {
        $('#topMasterTreatmentsList').html('<div class="p-3 text-sm text-gray-500 text-center">Type to search master treatments...</div>');
        return;
    }
    topMasterSearchTimer = setTimeout(function() {
        $.ajax({
            url: '{{ route("admin.treatment-hierarchy.search-master") }}',
            type: 'GET',
            data: { q: q },
            success: function(treatments) {
                if (treatments.length === 0) {
                    $('#topMasterTreatmentsList').html('<div class="p-3 text-sm text-gray-500 text-center">No treatments found</div>');
                } else {
                    var html = '';
                    treatments.forEach(function(t) {
                        html += '<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 text-sm flex justify-between items-center">';
                        html += '<span class="text-gray-800">' + $('<div>').text(t.Title).html() + '</span>';
                        html += '<button type="button" onclick="addTopLevelFromMaster(\'' + t.TreatmentTypeID + '\')" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">Add</button>';
                        html += '</div>';
                    });
                    $('#topMasterTreatmentsList').html(html);
                }
            }
        });
    }, 300);
});

function addTopLevelFromMaster(masterTreatmentId) {
    $.ajax({
        url: '{{ route("admin.treatment-hierarchy.add-from-master") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            ParentTreatmentTypeID: '00000000-0000-0000-0000-000000000000',
            MasterTreatmentTypeID: masterTreatmentId
        },
        success: function(res) {
            if (res.success) {
                closeAddFromMasterModal();
                Swal.fire('Success!', res.message, 'success').then(function() {
                    treatmentTable.ajax.reload();
                });
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error!', xhr.responseJSON?.message || 'Error adding treatment.', 'error');
        }
    });
}
</script>
@endsection
