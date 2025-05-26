<?php

namespace App\Repositories;

use App\Models\User;

interface UpdateProfilePhotoRepositoryInterface
{
    public function updateProfilePhoto( $user, $path , $arr) ;
}
