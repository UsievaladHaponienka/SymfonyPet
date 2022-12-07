<?php

namespace App\Controller\Group;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseGroupController extends AbstractController
{
    public function isAdmin(Group $group): bool
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user->getProfile()->getId() == $group->getAdmin()->getId();
    }
}