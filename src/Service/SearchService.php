<?php

namespace App\Service;

use App\Repository\GroupRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\Form\FormInterface;

class SearchService
{

    private GroupRepository $groupRepository;
    private ProfileRepository $profileRepository;

    public function __construct(GroupRepository $groupRepository, ProfileRepository $profileRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->profileRepository = $profileRepository;
    }

    public function searchGroups(string $searchString): array
    {
        return $this->groupRepository
            ->createQueryBuilder('q')
            ->where('q.title LIKE :searchString')
            ->orWhere('q.description LIKE :searchString')
            ->setParameter(':searchString', '%' . $searchString . '%')
            ->getQuery()
            ->getResult();
    }

    public function searchProfiles(string $searchString): array
    {
        return $this->profileRepository
            ->createQueryBuilder('q')
            ->where('q.username LIKE :searchString')
            ->orWhere('q.description LIKE :searchString')
            ->setParameter(':searchString', '%' . $searchString . '%')
            ->getQuery()
            ->getResult();
    }
}