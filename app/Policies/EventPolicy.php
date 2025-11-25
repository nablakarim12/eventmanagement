<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\EventOrganizer;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage (approve/reject registrations) for the event.
     * Works for both Admin and EventOrganizer
     */
    public function manage(Admin|EventOrganizer $user, Event $event): bool
    {
        // If Admin
        if ($user instanceof Admin) {
            return $user->id === $event->admin_id;
        }
        
        // If EventOrganizer
        if ($user instanceof EventOrganizer) {
            return $user->id === $event->organizer_id;
        }
        
        return false;
    }

    /**
     * Determine whether the admin can view any events.
     */
    public function viewAny(Admin $admin): bool
    {
        return true; // All admins can view events
    }

    /**
     * Determine whether the admin can view the event.
     */
    public function view(Admin $admin, Event $event): bool
    {
        return true; // All admins can view any event
    }

    /**
     * Determine whether the admin can create events.
     */
    public function create(Admin $admin): bool
    {
        return true; // All admins can create events
    }

    /**
     * Determine whether the admin can update the event.
     */
    public function update(Admin $admin, Event $event): bool
    {
        return $admin->id === $event->admin_id;
    }

    /**
     * Determine whether the admin can delete the event.
     */
    public function delete(Admin $admin, Event $event): bool
    {
        return $admin->id === $event->admin_id;
    }

    /**
     * Determine whether the admin can restore the event.
     */
    public function restore(Admin $admin, Event $event): bool
    {
        return $admin->id === $event->admin_id;
    }

    /**
     * Determine whether the admin can permanently delete the event.
     */
    public function forceDelete(Admin $admin, Event $event): bool
    {
        return $admin->id === $event->admin_id;
    }
}
