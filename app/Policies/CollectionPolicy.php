<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;

class CollectionPolicy
{
    /**
     * Determine whether the user can view any collections.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the collection.
     */
    public function view(User $user, Collection $collection): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create collections.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the collection.
     */
    public function update(User $user, Collection $collection): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the collection.
     */
    public function delete(User $user, Collection $collection): bool
    {
        return $user->isAdmin();
    }
}
