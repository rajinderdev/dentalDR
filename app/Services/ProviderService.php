<?php

namespace App\Services;

use App\Http\Resources\ProviderResource;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Helpers\EntityDataHelper;
class ProviderService
{
    // Add your business logic for Provider here.
    public function getProviders($perPage = 50, $search = null)
    {
        $providerList = Provider::when($search, function (Builder $query) use ($search) {
            $query->where('Name', 'like', "%$search%")
                  ->orWhere('Email', 'like', "%$search%")
                  ->orWhere('PhoneNumber', 'like', "%$search%");
        })->paginate($perPage);
        return [
            'providers' => $providerList,
            'pagination' => [
                'currentPage' => $providerList->currentPage(),
                'perPage' => $providerList->perPage(),
                'total' => $providerList->total(),
            ]
        ];
    }

    /**
     * Create a new provider record with associated user account.
     *
     * @param array $data The validated data for creating the provider
     * @return Provider The newly created provider model
     */
    public function createProvider(array $data): Provider
    {
        return DB::transaction(function () use ($data) {
            // Extract password from provider data if present
            $password = $data['Password'] ?? null;
            unset($data['Password']);

            $user = null;
            
            // Create user account if password is provided
            if ($password && isset($data['Email'])) {
                // Set default ClinicID if not provided
                if (empty($data['ClinicID'])) {
                    $data['ClinicID'] = "7B13B11A-9A05-4E1D-A547-7E5A2F2B3FD2";
                }
                $userData = [
                    'UserName' => $data['Email'], // Use email as username
                    'Email' => $data['Email'],
                    'Name' => $data['ProviderName'] ?? 'Provider',
                    'Password' => $password, // Will be automatically hashed
                    'Mobile' => $data['PhoneNumber'] ?? null,
                    'ClientID' => $data['ClinicID'],
                    'RoleID' => "5973E31F-A329-4E2D-8541-C98D1E22CF81", // Provider role
                    'IsDeleted' => false,
                ];
                $userData = EntityDataHelper::prepareForCreation($userData);
                $user = User::create($userData);
                
                // Link user to provider
                $data['UserID'] = $user->UserID;
            }

            // Create provider
            return Provider::create($data);
        });
    }

    /**
     * Update an existing provider record.
     *
     * @param Provider $provider The provider model to update
     * @param array $data The validated data for updating the provider
     * @return Provider The updated provider model
     */
    public function updateProvider(Provider $provider, array $data): Provider
    {
        $provider->update($data);
        return $provider;
    }
}
