<?php

namespace App\Repositories;

use App\Models\User;

class UpdateProfilePhotoRepository implements UpdateProfilePhotoRepositoryInterface
{

    public function updateProfilePhoto($user,  $path,  $arr)
    {
        $user->update($arr);
        $user->profile_photo = $path;
        $user->save();
        return $user;
    }
}
