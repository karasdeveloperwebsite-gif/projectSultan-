<?php

namespace App\Policies;

use App\Models\GalleryItem;
use App\Models\User;

class GalleryItemPolicy
{
    /**
     * Determine whether the user can view any gallery items.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the gallery item.
     */
    public function view(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create gallery items.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the gallery item.
     */
    public function update(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the gallery item.
     */
    public function delete(User $user, GalleryItem $galleryItem): bool
    {
        return $user->isAdmin();
    }
}
