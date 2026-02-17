<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Services\PackageManagementService;
use Exception;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;

/**
 * @group Package
 * PackageController handles the CRUD operations for packages and package services.
 */
class PackageController extends Controller
{
    use ApiResponse;

    public function __construct(private PackageManagementService $packageService)
    {
    }

    /**
     * @group Package
     *
     * @method GET
     *
     * List all packages with pagination and filtering
     *
     * @queryParam search string Search term for package name or code. Example: "dental"
     * @queryParam status string Filter by status (active/inactive). Example: "active"
     * @queryParam per_page int Items per page. Example: 50
     *
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "packages": [
     *             {
     *                 "PackageID": "550e8400-e29b-41d4-a716-446655440000",
     *                 "PackageName": "Dental Checkup Package",
     *                 "PackageCode": "DCP-001",
     *                 "Price": 199.99,
     *                 "Status": "active"
     *             }
     *         ],
     *         "pagination": {
     *             "current_page": 1,
     *             "per_page": 50,
     *             "total": 1
     *         }
     *     }
     * }
     *
     * @response 500 {"success": false, "message": "An error occurred while retrieving packages."}
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', env('DEFAULT_PER_PAGE', 50));
            $search = $request->query('search');
            $status = $request->query('status');

            $packageList = $this->packageService->getAllPackages($perPage, $search, $status);

            return $this->successResponse([
                'packages' => PackageResource::collection($packageList['packages']),
                'pagination' => $packageList['pagination']
            ]);
        } catch (Exception $e) {
            Log::error('Package list error: ' . $e->getMessage());
            return $this->errorResponse([
                'message' => 'An error occurred while retrieving packages.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @group Package
     *
     * @method GET
     *
     * Get a specific package by ID
     *
     * @urlParam id string required The ID of the package. Example: 550e8400-e29b-41d4-a716-446655440000
     *
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "package": {
     *             "PackageID": "550e8400-e29b-41d4-a716-446655440000",
     *             "PackageName": "Dental Checkup Package",
     *             "PackageCode": "DCP-001",
     *             "Description": "Complete dental checkup package",
     *             "Price": 199.99,
     *             "Status": "active",
     *             "services": [
     *                 {
     *                     "PackageServiceID": "660e8400-e29b-41d4-a716-446655440001",
     *                     "TreatmentID": "770e8400-e29b-41d4-a716-446655440002",
     *                     "TreatmentName": "Dental Checkup",
     *                     "QuantityLimit": 1
     *                 }
     *             ]
     *         }
     *     }
     * }
     *
     * @response 404 {"success": false, "message": "Package not found"}
     * @response 500 {"success": false, "message": "An error occurred while retrieving the package."}
     */
    public function show(string $id)
    {
        try {
            $package = $this->packageService->getPackageById($id);
            
            return $this->successResponse([
                'package' => new PackageResource($package)
            ]);
        } catch (Exception $e) {
            Log::error('Package show error: ' . $e->getMessage());
             return $this->errorResponse([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @group Package
     *
     * @method POST
     *
     * Create a new package
     *
     * @bodyParam PackageName string required The name of the package. Example: Dental Checkup Package
     * @bodyParam PackageCode string required The unique code for the package. Example: DCP-001
     * @bodyParam Description string The description of the package. Example: Complete dental checkup package
     * @bodyParam Price numeric required The price of the package. Example: 199.99
     * @bodyParam Interval string The billing interval. Example: monthly
     * @bodyParam DiscountAmount numeric The discount amount. Example: 20.00
     * @bodyParam AdditionAmount numeric The additional amount. Example: 10.00
     * @bodyParam Status string required The status of the package (active/inactive). Example: active
     * @bodyParam services array The list of services included in the package.
     * @bodyParam services[].TreatmentID string required The ID of the treatment. Example: 770e8400-e29b-41d4-a716-446655440002
     * @bodyParam services[].QuantityLimit integer The quantity limit for the treatment. Example: 1
     *
     * @response 201 {
     *     "success": true,
     *     "message": "Package created successfully",
     *     "data": {
     *         "package": {
     *             "PackageID": "550e8400-e29b-41d4-a716-446655440000",
     *             "PackageName": "Dental Checkup Package",
     *             "PackageCode": "DCP-001",
     *             "Status": "active"
     *         }
     *     }
     * }
     *
     * @response 422 {"success": false, "message": "Validation error", "errors": {"PackageName": ["The package name field is required."]}}
     * @response 500 {"success": false, "message": "Failed to create package"}
     */
    public function store(StorePackageRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $package = $this->packageService->createPackage($validatedData);
            
            return $this->successResponse([
                'message' => 'Package created successfully',
                'package' => new PackageResource($package)
            ]);
        } catch (Exception $e) {
            Log::error('Package store error: ' . $e->getMessage());
            return $this->errorResponse([
                'message' => 'Failed to create package',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @group Package
     *
     * @method PUT
     *
     * Update a package
     *
     * @urlParam id string required The ID of the package. Example: 550e8400-e29b-41d4-a716-446655440000
     * @bodyParam PackageName string The name of the package. Example: Updated Dental Checkup Package
     * @bodyParam PackageCode string The unique code for the package. Example: DCP-001-UPDATED
     * @bodyParam Description string The description of the package.
     * @bodyParam Price numeric The price of the package.
     * @bodyParam Status string The status of the package (active/inactive).
     * @bodyParam services array The list of services to update or create.
     * @bodyParam services[].PackageServiceID string The ID of the package service (for updates).
     * @bodyParam services[].TreatmentID string required The ID of the treatment.
     * @bodyParam services[].QuantityLimit integer The quantity limit for the treatment.
     * @bodyParam deleted_services array The list of package service IDs to delete.
     *
     * @response 200 {
     *     "success": true,
     *     "message": "Package updated successfully",
     *     "data": {
     *         "package": {
     *             "PackageID": "550e8400-e29b-41d4-a716-446655440000",
     *             "PackageName": "Updated Dental Checkup Package",
     *             "Status": "active"
     *         }
     *     }
     * }
     *
     * @response 404 {"success": false, "message": "Package not found"}
     * @response 422 {"success": false, "message": "Validation error"}
     * @response 500 {"success": false, "message": "Failed to update package"}
     */
    public function update(UpdatePackageRequest $request, string $id)
    {
        try {
            $validatedData = $request->validated();
            $package = $this->packageService->updatePackage($id, $validatedData);
            
            return $this->successResponse([
                'message' => 'Package updated successfully',
                'package' => new PackageResource($package)
            ]);
     
        } catch (Exception $e) {
            Log::error('Package update error: ' . $e->getMessage());
            return $this->errorResponse([
                'message' => 'Failed to update package',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @group Package
     *
     * @method DELETE
     *
     * Delete a package
     *
     * @urlParam id string required The ID of the package to delete. Example: 550e8400-e29b-41d4-a716-446655440000
     *
     * @response 200 {
     *     "success": true,
     *     "message": "Package deleted successfully"
     * }
     *
     * @response 404 {"success": false, "message": "Package not found"}
     * @response 500 {"success": false, "message": "Failed to delete package"}
     */
    public function destroy(string $id)
    {
        try {
            $this->packageService->deletePackage($id);
            
            return $this->successResponse([
                'message' => 'Package deleted successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Package delete error: ' . $e->getMessage());
            return $this->errorResponse([
                'message' => 'Failed to delete package',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
