<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Repository;

use App\Auth\Domain\AccountIdentifier;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocalUser>
 *
 * @method null|LocalUser find($id, $lockMode = null, $lockVersion = null)
 * @method null|LocalUser findOneBy(array $criteria, array $orderBy = null)
 * @method LocalUser[]    findAll()
 * @method LocalUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalUser::class);
    }

    public function store(LocalUser $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(LocalUser $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByIdentifier(AccountIdentifier $identifier): ?LocalUser
    {
        return $this->findOneBy(['identifierType' => $identifier->type, 'identifierValue' => $identifier->value]);
    }
}
