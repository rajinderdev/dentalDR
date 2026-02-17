<?php

namespace App\Services;

use App\Http\Resources\PatientTreatmentResource;
use App\Models\PatientTreatmentsDone;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TreatmentService
{
    public function getOngoingTreatmentsForToday($doctorIds = [], $providerId = null)
    {
        $today = Carbon::today()->toDateString();

        $user = Auth::user();
        $isDoctor = $user && $user->role && strtolower($user->role->RoleName) === 'doctor';

        // For doctor users: ALWAYS enforce their own ProviderID (ignore request filters).
        if ($isDoctor) {
            $providerId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            $doctorIds = [];
        } else {
            // For admin/staff: UI might send ProviderIDs or UserIDs; normalize to ProviderIDs.
            if (!empty($doctorIds)) {
                $doctorIds = is_array($doctorIds) ? $doctorIds : [$doctorIds];
                $doctorIds = array_values(array_filter(array_map('strval', $doctorIds), fn ($v) => trim($v) !== ''));

                $providerIdsByProviderId = Provider::whereIn('ProviderID', $doctorIds)->pluck('ProviderID')->all();
                $providerIdsByUserId = Provider::whereIn('UserID', $doctorIds)->pluck('ProviderID')->all();
                $doctorIds = array_values(array_unique(array_merge($providerIdsByProviderId, $providerIdsByUserId)));
            }

            if (!empty($providerId)) {
                $providerId = (string) $providerId;
                $normalizedProviderId = Provider::where('ProviderID', $providerId)->value('ProviderID');
                if (!$normalizedProviderId) {
                    $normalizedProviderId = Provider::where('UserID', $providerId)->value('ProviderID');
                }
                $providerId = $normalizedProviderId ?: $providerId;
            }
        }

        $appointments = PatientTreatmentsDone::
        when(!empty($doctorIds), function ($query) use ($doctorIds) {
            $query->whereIn('ProviderID', $doctorIds);
        })
        ->when($providerId, function ($query) use ($providerId) {
            $query->where('ProviderID', $providerId);
        })
        ->whereDate('TreatmentDate', $today)
        ->where('IsCompleted', 0)
        ->where('WaitingAreaFlag', 0)
        ->orderBy('AddedOn', 'desc') // Sort by latest records first
        ->get();
        $parents = $appointments->where('ParentPatientTreatmentDoneID', '00000000-0000-0000-0000-000000000000')->values();
        $appointmentsCount = $appointments->count();
        // Attach children to their respective parents
        $parents = $parents->map(function ($parent) use ($appointments) {
            $children = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $parent->PatientTreatmentDoneID)->get();
            $parent->children = $children;
            return $parent;
        });
        return ['count' => $appointmentsCount, 'list' => PatientTreatmentResource::collection($parents)];
    }

    public function getCompletedTreatmentsForToday($doctorIds = [], $providerId = null)
    {
        $today = Carbon::today()->toDateString();

        $user = Auth::user();
        $isDoctor = $user && $user->role && strtolower($user->role->RoleName) === 'doctor';

        // For doctor users: ALWAYS enforce their own ProviderID (ignore request filters).
        if ($isDoctor) {
            $providerId = Provider::where('UserID', $user->UserID)->value('ProviderID');
            $doctorIds = [];
        } else {
            // For admin/staff: UI might send ProviderIDs or UserIDs; normalize to ProviderIDs.
            if (!empty($doctorIds)) {
                $doctorIds = is_array($doctorIds) ? $doctorIds : [$doctorIds];
                $doctorIds = array_values(array_filter(array_map('strval', $doctorIds), fn ($v) => trim($v) !== ''));

                $providerIdsByProviderId = Provider::whereIn('ProviderID', $doctorIds)->pluck('ProviderID')->all();
                $providerIdsByUserId = Provider::whereIn('UserID', $doctorIds)->pluck('ProviderID')->all();
                $doctorIds = array_values(array_unique(array_merge($providerIdsByProviderId, $providerIdsByUserId)));
            }

            if (!empty($providerId)) {
                $providerId = (string) $providerId;
                $normalizedProviderId = Provider::where('ProviderID', $providerId)->value('ProviderID');
                if (!$normalizedProviderId) {
                    $normalizedProviderId = Provider::where('UserID', $providerId)->value('ProviderID');
                }
                $providerId = $normalizedProviderId ?: $providerId;
            }
        }

        $appointments = PatientTreatmentsDone::when(!empty($doctorIds), function ($query) use ($doctorIds) {
            $query->whereIn('ProviderID', $doctorIds);
        }) 
        ->when($providerId, function ($query) use ($providerId) {
            $query->where('ProviderID', $providerId);
        })
        ->whereDate('TreatmentDate', $today)
        ->where('IsCompleted', 1)
        ->orderBy('AddedOn', 'desc') // Sort by latest records first
        ->get();
        $parents = $appointments->where('ParentPatientTreatmentDoneID', '00000000-0000-0000-0000-000000000000')->values();
        $appointmentsCount = $appointments->count();
        // Attach children to their respective parents
        $parents = $parents->map(function ($parent) use ($appointments) {
            $children = PatientTreatmentsDone::where('ParentPatientTreatmentDoneID', $parent->PatientTreatmentDoneID)->get();
            if($children->isNotEmpty()){
                $parent->children = $children;
            }
            else{
                $children = PatientTreatmentsDone::where('PatientTreatmentDoneID', $parent->PatientTreatmentDoneID)->get();
                $parent->children = $children;
            }
            return $parent;
        });

        return ['count' => $appointmentsCount, 'list' => PatientTreatmentResource::collection($parents)];
    }
}