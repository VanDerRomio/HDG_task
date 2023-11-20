<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;

    protected ?User $authenticatedUser;

    protected function setAuthenticatedUser(Request $request): void{
        $login = $request->getUser();

        $this->authenticatedUser = User::query()
            ->where('email', $login)
            ->first();
    }
}
