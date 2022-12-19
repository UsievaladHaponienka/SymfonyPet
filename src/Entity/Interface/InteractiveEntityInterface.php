<?php

namespace App\Entity\Interface;

use App\Entity\Profile;

/**
 * Entity, which can be changed by user under certain conditions, should implement this interface.
 */
interface InteractiveEntityInterface
{
    public const CREATE_ACTION_CODE = 'create';
    public const VIEW_ACTION_CODE = 'view';
    public const EDIT_ACTION_CODE = 'edit';
    public const DELETE_ACTION_CODE = 'delete';
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