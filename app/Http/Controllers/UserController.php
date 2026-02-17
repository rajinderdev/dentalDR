<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Provider;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\EntityDataHelper;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    use ApiResponse;
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * @group User
     *
     * @method GET
     *
     * List all user
     *
     * @get /
     *
     * @response 200 scenario="Success" {
     *     {
     *         "data": {
     *             "users": [
     *                 {
     *                     "id": 1,
     *                     "role_id": 1,
     *                     "client_id": 1,
     *                     "user_name": "Example Name",
     *                     "email": "user@example.com",
     *                     "name": "Example Name",
     *                     "is_deleted": true,
     *                     "mobile": "Example value"
     *                 }
     *             ],
     *             "pagination": {
     *                 "current_page": 1,
     *                 "per_pages": 50,
     *                 "total": 100
     *             }
     *         }
     *     }
     * }
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', env('DEFAULT_PER_PAGE', 50));

            $userList = $this->userService->getUsers($perPage);

            return $this->successResponse(['users' => UserResource::collection($userList['users']), 'pagination' => $userList['pagination']]);
        } catch (Exception $e) {
            // Catch any exception and return a generic error message
            return $this->errorResponse(['message' => 'Something went wrong. Please try again later.']);
        }
    }

    /**
     * @group User
     *
     * @method GET
     *
     * Create user
     *
     * @get /create
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @group User
     *
     * @method POST
     *
     * Create a new user
     *
     * @post /
     *
     * @bodyParam RoleID string required. Maximum length: 255. Example: "Example RoleID"
     * @bodyParam ClientID string required. Maximum length: 255. Example: "Example ClientID"
     * @bodyParam UserName string required. Maximum length: 255. Must be unique. Example: "Example UserName"
     * @bodyParam Password string required. Minimum length: 8. Example: "Example Password"
     * @bodyParam Email string required. Must be a valid email address. Must be unique. Example: "Example Email"
     * @bodyParam Name string required. Maximum length: 255. Example: "Example Name"
     * @bodyParam Mobile string optional. nullable. Maximum length: 15. Example: "Example Mobile"
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);

        return $this->successResponse(['message' => 'User created successfully', 'data' => $user], successCode: 201);
    }

    /**
     * @group User
     *
     * @method GET
     *
     * Get a specific user
     *
     * @get /{id}
     *
     * @urlParam id integer required. The ID of the user to retrieve. Example: 1
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        //return Users::all();
    }

    /**
     * @group User
     *
     * @method GET
     *
     * Edit user
     *
     * @get /{id}/edit
     *
     * @urlParam id integer required. The ID of the user to use. Example: 1
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * @group User
     *
     * @method PUT
     *
     * Update an existing user
     *
     * @put /{id}
     *
     * @urlParam id integer required. The ID of the user to update. Example: 1
     *
     * @bodyParam RoleID string optional. Maximum length: 255. Example: "Example RoleID"
     * @bodyParam ClientID string optional. Maximum length: 255. Example: "Example ClientID"
     * @bodyParam UserName string optional. Maximum length: 255. Must be unique. Example: "Example UserName"
     * @bodyParam Password string optional. nullable. Minimum length: 8. Example: "Example Password"
     * @bodyParam Email string optional. Must be a valid email address. Must be unique. Example: "Example Email"
     * @bodyParam Name string optional. Maximum length: 255. Example: "Example Name"
     * @bodyParam Mobile string optional. nullable. Maximum length: 15. Example: "Example Mobile"
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 404 {"message": "Resource not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $user->update($validated);

        return $this->successResponse(['message' => 'User updated successfully', 'data' => $user], successCode: 200);
    }

    /**
     * @group User
     *
     * @method DELETE
     *
     * Delete a user
     *
     * @delete /{id}
     *
     * @urlParam id integer required. The ID of the user to delete. Example: 1
     *
     * @response 400 {"message": "Validation error", "errors": {"field": ["Error message"]}}
     * @response 404 {"message": "Resource not found"}
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
   public function getUser()
{
    $users = User::get();

    foreach ($users as $user) {
        $provider = Provider::where('Email', $user->Email)->first();
        if ($provider) {
            Provider::where('Email', $user->Email)->update(['UserID' => $user->UserID]);
        }
    }

    $providers = Provider::whereNull('UserID')->get();
    foreach ($providers as $provider) {
        $password = strtok($provider->Email, '@');
        $body = [
             'RoleID' => '5973E31F-A329-4E2D-8541-C98D1E22CF81',
            'ClientID' => $provider->ClinicID,
            'UserName' => $provider->Email,
            'Password' => $password,
            'Email' => $provider->Email,
            'Name' => $provider->ProviderName,
            'Mobile' => $provider->PhoneNumber,
            'CreatedOn' => now(),
            'CreatedBy' => Auth::user()->UserID,
            'LastUpdatedBy' => Auth::user()->UserID,
            'LastUpdatedOn' => now(),
        ];
      
        $data = EntityDataHelper::prepareForCreation($body);
        $user = User::create($data);
        Provider::where('ProviderID', $provider->ProviderID)->update(['UserID' => $user->UserID]);
        
    }
    $providers = Provider::whereNull('UserID')->get();
    // Convert collection to array safely
    $providersArray = $providers ? $providers->toArray() : [];

    // Clean UTF-8 characters only if array not empty
    if (!empty($providersArray)) {
        array_walk_recursive($providersArray, function (&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        });
    }

    return $this->successResponse([
        'message' => 'success',
        'data' => $providersArray,
        'total' => count($providersArray)
    ], successCode: 200);
}


}
