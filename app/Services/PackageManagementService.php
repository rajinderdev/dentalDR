<?php

namespace App\Services;

use App\Models\Package;
use App\Models\PackageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\EntityDataHelper;
class PackageManagementService
{
    public function getAllPackages($perPage, $search = null, $status = null)
    {
        $query = Package::when($status, function ($q) use ($status) {
                return $q->where('Status', $status);
            })
            ->when($search, function ($q) use ($search) {
                $searchTerm = '%' . $search . '%';
                return $q->where('PackageName', 'like', $searchTerm)
                    ->orWhere('PackageCode', 'like', $searchTerm);
            });

        $packages = $query->paginate($perPage);
        
        return [
            'packages' => $packages->getCollection(),
            'pagination' => [
                'current_page' => $packages->currentPage(),
                'per_page' => $packages->perPage(),
                'total' => $packages->total(),
                'last_page' => $packages->lastPage(),
            ]
        ];
    }

    public function getPackageById($id)
    {
        return Package::with('services.treatmentTypeHierarchy')->findOrFail($id);
    }

    public function createPackage($data)
    {
        return DB::transaction(function () use ($data) {
            // Create the package
            $dataToPersist = EntityDataHelper::prepareForCreation($data);
            $package = Package::create([
                'PackageID' => (string) Str::uuid(),
                'ClinicID' => $dataToPersist['ClinicID'] ?? Auth::user()->ClinicID,
                'PackageName' => $dataToPersist['PackageName'],
                'PackageCode' => $dataToPersist['PackageCode'],
                'Description' => $dataToPersist['Description'] ?? null,
                'Price' => $dataToPersist['Price'],
                'Interval' => $dataToPersist['Interval'] ?? null,
                'DiscountAmount' => $dataToPersist['DiscountAmount'] ?? 0,
                'AdditionAmount' => $dataToPersist['AdditionAmount'] ?? 0,
                'Status' => $dataToPersist['Status'],
                'CreatedBy' =>  $dataToPersist['CreatedBy'],
                'CreatedOn' => $dataToPersist['CreatedOn'],
                'LastUpdatedBy' => $dataToPersist['LastUpdatedBy'],
                'LastUpdatedOn' => $dataToPersist['LastUpdatedOn'],
            ]);
            return $package;
            // Add services if provided
            // if (!empty($data['services'])) {
            //     $this->syncPackageServices($package, $dataToPersist['services']);
            // }

            // return $package->load('services.treatmentTypeHierarchy');
        });
    }

    public function updatePackage($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $package = Package::findOrFail($id);
            
            // Update package details
            $dataToPersist = EntityDataHelper::prepareForUpdate($data);     
            $package->update($dataToPersist);
            
            // if (isset($data['services'])) {
            //     $this->syncPackageServices($package, $dataToPersist['services']);
            // }
            $package->fresh();
            return $package;
        });
    }

    public function deletePackage($id)
    {
        $package = Package::findOrFail($id);
        
       
        $package->update([
            'IsDeleted' => true,
            'LastUpdatedBy' => Auth::user()->id,
            'LastUpdatedOn' => now(),
        ]);
        
        // $package->services()->update([
        //     'IsDeleted' => true,
        //     'LastUpdatedBy' => Auth::user()->id,
        //     'LastUpdatedOn' => now(),
        // ]);

        return true;
    }

    protected function syncPackageServices($package, $services)
    {
        foreach ($services as $service) {
            $data = EntityDataHelper::prepareForCreation($service);
            if (isset($service['PackageServiceID'])) {
                // Update existing service
                $package->services()
                    ->where('PackageServiceID', $service['PackageServiceID'])
                    ->update([
                        'TreatmentName' => $data['TreatmentName'],
                        'TreatmentTypeID' => $data['TreatmentTypeID'],
                        'QuantityLimit' => $data['QuantityLimit'],
                        'LastUpdatedBy' => $data['LastUpdatedBy'],
                        'LastUpdatedOn' => $data['LastUpdatedOn'],
                    ]);
            } else {
                // Create new service
                $package->services()->create([
                    'PackageServiceID' => (string) Str::uuid(),
                    'ClinicID' => $package->ClinicID,
                    'TreatmentTypeID' => $data['TreatmentTypeID'],
                    'TreatmentName' => $data['TreatmentName'],
                    'QuantityLimit' => $data['QuantityLimit'],
                    'CreatedBy' => $data['CreatedBy'],
                    'CreatedOn' => $data['CreatedOn'],
                    'LastUpdatedBy' => $data['LastUpdatedBy'],
                    'LastUpdatedOn' =>  $data['CreatedOn'],
                    
                ]);
            }
        }
    }
}
