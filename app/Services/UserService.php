<?php

namespace App\Services;

use App\DTO\User\PostUserForm;
use App\DTO\User\PutUserForm;
use App\Models\User;

class UserService
{
    /**
     * @param PostUserForm $form
     * @return User|false
     */
    public function create(PostUserForm $form): User|false{
        $user = new User();

        $user->name     = $form->name;
        $user->email    = $form->email;
        $user->password = $form->password;
        $user->role     = $form->role;

        if($user->save()){
            return $user;
        }

        return false;
    }

    /**
     * @param User $user
     * @param PutUserForm $form
     * @return User|false
     */
    public function update(User $user, PutUserForm $form): User|false{
        if(!$form->name && !$form->email && !$form->password && !$form->role) {
            return $user;
        }

        if ($form->name) {
            $user->name = $form->name;
        }

        if ($form->email) {
            $user->email = $form->email;
        }

        if ($form->password) {
            $user->password = $form->password;
        }

        if ($form->role) {
            $user->role = $form->role;
        }

        if ($user->save()) {
            return $user;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool{
        if($user->delete()){
            return true;
        }

        return false;
    }
}
