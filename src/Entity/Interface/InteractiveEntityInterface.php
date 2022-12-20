<?php

namespace App\Entity\Interface;

use App\Entity\Profile;

/**
 * Entity, which can be changed by user under certain conditions, should implement this interface.
 */
interface InteractiveEntityInterface
{
    public const VIEW_ACTION_CODE = 'view';
    public const EDIT_ACTION_CODE = 'edit';
    public const DELETE_ACTION_CODE = 'delete';

    public const ADD_CHILD_ENTITY_ACTION = 'add_child';
    public const REMOVE_CHILD_ENTITY_CODE = 'remove_child';

    public const ACCEPT_ACTION_CODE = 'accept';

    /**
     * Check if entity action is allowed for $profile
     *
     * @param Profile $profile
     * @param string|null $actionCode
     * @return bool
     */
    public function isActionAllowed(Profile $profile, string $actionCode = null): bool;
}