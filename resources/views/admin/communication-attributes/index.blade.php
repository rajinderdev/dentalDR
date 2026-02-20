@extends('layouts.admin')
@push('styles')
<style>
    .toggle-switch { position: relative; display: inline-block; width: 48px; height: 24px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ef4444; border-radius: 24px; transition: .3s; }
    .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; border-radius: 50%; transition: .3s; }
    .toggle-switch input:checked + .toggle-slider { background-color: #22c55e; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(24px); }
    .tab-btn.active { border-bottom-color: #2563eb; color: #2563eb; font-weight: 600; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
</style>
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Communication Attributes</h1>
        <button type="button" id="btnUpdateChanges" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            Update Changes
        </button>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <div class="flex space-x-6">
            <button type="button" class="tab-btn active pb-3 px-1 text-sm font-medium border-b-2 border-transparent transition-colors" data-tab="sms">SMS</button>
            <button type="button" class="tab-btn pb-3 px-1 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors" data-tab="email">Email</button>
        </div>
    </div>

    <form id="communicationForm">
        @csrf
        <!-- SMS Tab -->
        <div class="tab-content active" id="tab-sms">
            @if($smsAutomatic->count())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Automatic</h2>
                @foreach($smsAutomatic as $category => $items)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-blue-600 uppercase tracking-wide mb-3">{{ str_replace('_', ' ', $category) }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 w-1/2">Description</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute1 ?: 'Value' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute2 ?: 'Scheduled Time' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">ON / OFF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $item->Description }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute1)
                                        <input type="text"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value1]"
                                               value="{{ $item->AttributeValue1 ?? $item->DefaultAttributeValue1 }}"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute2)
                                        <input type="time"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value2]"
                                               value="{{ $item->AttributeValue2 ?? $item->DefaultAttributeValue2 }}"
                                               class="w-28 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="items[{{ $item->ClinicCommunicationMasterID }}][active]" value="0">
                                            <input type="checkbox"
                                                   name="items[{{ $item->ClinicCommunicationMasterID }}][active]"
                                                   value="1"
                                                   {{ ($item->IsActive !== null ? $item->IsActive : false) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($smsManual->count())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Manual</h2>
                @foreach($smsManual as $category => $items)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-blue-600 uppercase tracking-wide mb-3">{{ str_replace('_', ' ', $category) }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 w-1/2">Description</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute1 ?: 'Value' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute2 ?: 'Scheduled Time' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">ON / OFF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $item->Description }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute1)
                                        <input type="text"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value1]"
                                               value="{{ $item->AttributeValue1 ?? $item->DefaultAttributeValue1 }}"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute2)
                                        <input type="time"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value2]"
                                               value="{{ $item->AttributeValue2 ?? $item->DefaultAttributeValue2 }}"
                                               class="w-28 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="items[{{ $item->ClinicCommunicationMasterID }}][active]" value="0">
                                            <input type="checkbox"
                                                   name="items[{{ $item->ClinicCommunicationMasterID }}][active]"
                                                   value="1"
                                                   {{ ($item->IsActive !== null ? $item->IsActive : false) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Email Tab -->
        <div class="tab-content" id="tab-email">
            @if($emailAutomatic->count())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Automatic</h2>
                @foreach($emailAutomatic as $category => $items)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-blue-600 uppercase tracking-wide mb-3">{{ str_replace('_', ' ', $category) }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 w-1/2">Description</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute1 ?: 'Value' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute2 ?: 'Scheduled Time' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">ON / OFF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $item->Description }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute1)
                                        <input type="text"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value1]"
                                               value="{{ $item->AttributeValue1 ?? $item->DefaultAttributeValue1 }}"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute2)
                                        <input type="time"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value2]"
                                               value="{{ $item->AttributeValue2 ?? $item->DefaultAttributeValue2 }}"
                                               class="w-28 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="items[{{ $item->ClinicCommunicationMasterID }}][active]" value="0">
                                            <input type="checkbox"
                                                   name="items[{{ $item->ClinicCommunicationMasterID }}][active]"
                                                   value="1"
                                                   {{ ($item->IsActive !== null ? $item->IsActive : false) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($emailManual->count())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Manual</h2>
                @foreach($emailManual as $category => $items)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-blue-600 uppercase tracking-wide mb-3">{{ str_replace('_', ' ', $category) }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 w-1/2">Description</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute1 ?: 'Value' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">
                                        {{ $items->first()->Attribute2 ?: 'Scheduled Time' }}
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 w-1/6">ON / OFF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $item->Description }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute1)
                                        <input type="text"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value1]"
                                               value="{{ $item->AttributeValue1 ?? $item->DefaultAttributeValue1 }}"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->Attribute2)
                                        <input type="time"
                                               name="items[{{ $item->ClinicCommunicationMasterID }}][value2]"
                                               value="{{ $item->AttributeValue2 ?? $item->DefaultAttributeValue2 }}"
                                               class="w-28 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="items[{{ $item->ClinicCommunicationMasterID }}][active]" value="0">
                                            <input type="checkbox"
                                                   name="items[{{ $item->ClinicCommunicationMasterID }}][active]"
                                                   value="1"
                                                   {{ ($item->IsActive !== null ? $item->IsActive : false) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
@section('page_js')
<script>
$(document).ready(function() {
    // Tab switching
    $('.tab-btn').on('click', function() {
        var tabId = $(this).data('tab');
        $('.tab-btn').removeClass('active').addClass('text-gray-500');
        $(this).addClass('active').removeClass('text-gray-500');
        $('.tab-content').removeClass('active');
        $('#tab-' + tabId).addClass('active');
    });

    // Update Changes button
    $('#btnUpdateChanges').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('admin.communication-attributes.update') }}",
            type: 'POST',
            data: $('#communicationForm').serialize(),
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error!', res.message || 'Failed to update.', 'error');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Failed to update communication attributes.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMessage, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Update Changes');
            }
        });
    });
});
</script>
@endsection
