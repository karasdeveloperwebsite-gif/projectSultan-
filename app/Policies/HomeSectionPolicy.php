<?php

namespace App\Policies;

use App\Models\HomeSection;
use App\Models\User;

class HomeSectionPolicy
{
    /**
     * Determine whether the user can view any home sections.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the home section.
     */
    public function update(User $user, HomeSection $homeSection): bool
    {
        return $user->isAdmin();
    }
}
