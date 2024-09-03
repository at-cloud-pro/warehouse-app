<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Repository;

use App\Auth\Infrastructure\AccountManager\AccountManagerUser;
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

    public function remove(LocalUser $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addUserByAccountManagerUser(AccountManagerUser $identifier): void
    {
        $user = new LocalUser($identifier->type, $identifier->value);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findByAccountManagerUser(AccountManagerUser $identifier): ?LocalUser
    {
        return $this->findOneBy(['identifierType' => $identifier->type, 'identifierValue' => $identifier->value]);
    }
}
