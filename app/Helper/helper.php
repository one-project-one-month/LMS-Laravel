<?php

use App\Models\Role;

function get_role_id($role)
{
    $role_id = Role::where("role", $role)->first()->id;
    return $role_id;
}
function is_($role)
{
    $user = auth()->user(); // Get the authenticated user

    return $user && $user->role_id === get_role_id($role);
}
