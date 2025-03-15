<?php

namespace App\Repositories;

use App\Models\User;

class UpdateProfilePhotoRepository implements UpdateProfilePhotoRepositoryInterface
{
    public function updateProfilePhoto(User $user, ?string $path): bool
    {
        $user->profile_photo = $path;
        return $user->save();
    }
}
