<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context;

use App\AccountManager\Infrastructure\Doctrine\Entity\EmailConfirmation\ConfirmEmailCode;
use App\AccountManager\Infrastructure\Doctrine\Entity\User\User;
use App\AccountManager\Infrastructure\Doctrine\Repository\ConfirmEmailCodeRepository;
use App\AccountManager\Infrastructure\Doctrine\Repository\UserRepository;
use App\Tests\Behat\Helper\EntityFqcnMap;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

final class DatabaseContext implements Context
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Given('I have in a database the verification code :code for user :user_email')]
    public function iHaveInDatabaseVerificationCode(string $code, string $userEmail): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var ConfirmEmailCodeRepository $confirmEmailCodeRepository */
        $confirmEmailCodeRepository = $this->entityManager->getRepository(ConfirmEmailCode::class);

        $user = $userRepository->findOneBy(['email' => $userEmail]);
        Assert::assertNotNull($user);

        $confirmEmailCode = new ConfirmEmailCode($code, $user, new \DateTimeImmutable());
        $confirmEmailCodeRepository->add($confirmEmailCode);
    }

    #[Then('I see entity :entityName in the database:')]
    public function iSeeInDatabaseTable(string $entityName, PyStringNode $entityData): void
    {
        $entityFqcn = EntityFqcnMap::getByName($entityName);
        $repository = $this->entityManager->getRepository($entityFqcn);

        $criteria = json_decode($entityData->getRaw(), true);
        $entity = $repository->findOneBy($criteria);

        $message = sprintf(
            'Entity %s with parameters %s was not found in the database',
            $entityName,
            $entityData->getRaw()
        );
        Assert::assertNotNull($entity, $message);
    }

    #[Then('I do not see entity :entityName in the database:')]
    public function iDoNotSeeInDatabaseTable(string $entityName, PyStringNode $entityData): void
    {
        $entityFqcn = EntityFqcnMap::getByName($entityName);
        $repository = $this->entityManager->getRepository($entityFqcn);

        $criteria = json_decode($entityData->getRaw(), true);
        $entity = $repository->findOneBy($criteria);

        $message = sprintf(
            'Entity %s with parameters %s was not found in the database',
            $entityName,
            $entityData->getRaw()
        );
        Assert::assertNull($entity, $message);
    }

    #[Then('I do not see :entityName with ID :id in the database')]
    public function iDoNotSeeEntityWithIdInTheDatabase(string $entityName, string $id): void
    {
        $entityFqcn = EntityFqcnMap::getByName($entityName);
        $repository = $this->entityManager->getRepository($entityFqcn);
        $entity = $repository->find($id);

        $message = sprintf('Entity %s with id %s was found in the database', $entityName, $id);
        Assert::assertNull($entity, $message);
    }
}
