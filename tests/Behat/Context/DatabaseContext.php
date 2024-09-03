<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context;

use App\Tests\Behat\Helper\EntityClassMap;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Then;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

final class DatabaseContext implements Context
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Then('I see entity :entityName in the database:')]
    public function iSeeInDatabaseTable(string $entityName, PyStringNode $entityData): void
    {
        $entityFqcn = EntityClassMap::getByName($entityName);
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
        $entityFqcn = EntityClassMap::getByName($entityName);
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
        $entityFqcn = EntityClassMap::getByName($entityName);
        $repository = $this->entityManager->getRepository($entityFqcn);
        $entity = $repository->find($id);

        $message = sprintf('Entity %s with id %s was found in the database', $entityName, $id);
        Assert::assertNull($entity, $message);
    }
}
