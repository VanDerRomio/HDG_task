<?php

namespace App\Http\Controllers\Api;

use App\DTO\User\PostUserForm;
use App\DTO\User\PutUserForm;
use App\Helpers\ResponseStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function __construct(Request $request){
        $this->setAuthenticatedUser($request);
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::query()
            ->with('tasks')
            ->paginate(10);

        return $this->successResponse(new UserCollection($users));
    }

    /**
     * @param StoreUserRequest $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request, UserService $userService): JsonResponse
    {
        $postUserForm = PostUserForm::fromArray($request->validated());

        $user = $userService->create($postUserForm);

        if($user){
            return $this->successResponse(new UserResource($user));
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1013);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = Cache::remember(User::class . ":{$id}", 60 * 10, function() use($id) {
            return User::query()
                ->with('tasks')
                ->find($id);
        });

        if(!$user){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1011);
        }

        return $this->successResponse((new UserResource($user)));
    }

    /**
     * @param UpdateUserRequest $request
     * @param string $id
     * @param UserService $userService
     * @return JsonResponse
     */
    public function update(
        UpdateUserRequest $request,
        string $id,
        UserService $userService): JsonResponse
    {
        $putUserForm = PutUserForm::fromArray($request->validated(), $id);

        $user = User::query()
            ->findOrFail($id);

        $updatedUser = $userService->update($user, $putUserForm);

        if($updatedUser){
            return $this->successResponse(new UserResource($updatedUser));
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1014);
    }

    /**
     * @param string $id
     * @param UserService $userService
     * @return JsonResponse
     */
    public function destroy(string $id, UserService $userService): JsonResponse
    {
        $user = User::query()
            ->findOrFail($id);

        $isDeleted = $userService->delete($user);

        if($isDeleted){
            return $this->successResponse();
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1015);
    }
}
