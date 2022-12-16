<?php

namespace App\Repository;

use App\Entity\PrivacySettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrivacySettings>
 *
 * @method PrivacySettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivacySettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivacySettings[]    findAll()
 * @method PrivacySettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivacySettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivacySettings::class);
    }

    public function save(PrivacySettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PrivacySettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
