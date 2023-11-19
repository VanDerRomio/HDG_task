<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        $users = User::query()
            ->paginate(10);

        return new UserCollection($users);
    }

    /**
     * @param StoreUserRequest $request
     * @return JsonResponse|JsonResource
     */
    public function store(StoreUserRequest $request): JsonResponse|JsonResource
    {
        $data = $request->validated();
    }

    /**
     * @param string $id
     * @return JsonResponse|JsonResource
     */
    public function show(string $id): JsonResponse|JsonResource
    {
        $user = Cache::remember(User::class . ":{$id}", 60 * 10, function() use($id) {
            return User::query()
                ->first($id);
        });

        if(!$user){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1011);
        }

        return new UserResource($user);
    }

    /**
     * @param UpdateUserRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::query()
            ->firstOrFail($id);

        if($user->delete()){
            return $this->successResponse();
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1013);
    }
}
