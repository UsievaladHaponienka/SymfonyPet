<?php

namespace App\Repository;

use App\Entity\GroupRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupRequest>
 *
 * @method GroupRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupRequest[]    findAll()
 * @method GroupRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupRequest::class);
    }

    public function save(GroupRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GroupRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
