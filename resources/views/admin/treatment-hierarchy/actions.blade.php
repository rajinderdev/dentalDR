<div class="flex items-center justify-center">
    <button type="button" onclick="viewSteps('{{ $treatment->TreatmentTypeID }}', '{{ addslashes($treatment->Title) }}')"
            class="text-green-600 hover:text-green-800" title="Add Step">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12"/></svg>
    </button>
</div>
