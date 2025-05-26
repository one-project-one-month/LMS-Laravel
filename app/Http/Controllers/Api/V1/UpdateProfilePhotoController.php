<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralUserResource;
use App\Models\User;
use App\Repositories\UpdateProfilePhotoRepositoryInterface;
use App\Traits\ResponseTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class UpdateProfilePhotoController extends Controller
{
    use ResponseTraits;

    protected $updateProfilePhotoRepository;

    public function __construct(UpdateProfilePhotoRepositoryInterface $updateProfilePhotoRepository)
    {
        $this->updateProfilePhotoRepository = $updateProfilePhotoRepository;
    }

    public function __invoke(Request $request, User $user)
    {
        $path = $user->profile_photo;
        if ($request->hasFile('profile_photo')) {


            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $file = $request->file('profile_photo');
            $extension = $file->getClientOriginalExtension();
            $emailPart = str_replace(['@', '.'], '_', $user->email);
            $uniqueNumber = time();
            $filename = "profile_photo_{$emailPart}_{$user->id}_{$uniqueNumber}." . $extension;

            $path = $file->storeAs('profile_photos', $filename, 'public');
        }

        $data = $request->all();
        $arr = Arr::except($data, "profile_photo");
        $result =    $this->updateProfilePhotoRepository->updateProfilePhoto($user, $path, $arr);

        // return $this->successResponse(
        //     'Profile photo updated successfully',
        //     ['profile_photo' => $path],
        //     200
        // );



        //  $this->updateProfilePhotoRepository->updateProfilePhoto($user, null);

        return GeneralUserResource::make($result)->additional(["message" => "User update successfully"]);    }
}
