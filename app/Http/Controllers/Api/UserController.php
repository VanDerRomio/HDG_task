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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{

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
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $postUserForm = PostUserForm::fromArray($data);
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
                ->first($id);
        });

        if(!$user){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1011);
        }

        return $this->successResponse((new UserResource($user)));
    }

    /**
     * @param UpdateUserRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        $putUserForm = PutUserForm::fromArray($data);
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
