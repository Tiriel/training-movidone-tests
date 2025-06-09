<?php

namespace App\Autocompleter;

use App\Entity\Organization;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'organization'])]
class OrganizationAutocompleter implements EntityAutocompleterInterface
{

    /**
     * @inheritDoc
     */
    public function getEntityClass(): string
    {
        return Organization::class;
    }

    /**
     * @inheritDoc
     */
    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository->createQueryBuilder('o')
            ->where('o.name LIKE :query OR o.presentation LIKE :query')
            ->setParameter('query', '%'.$query.'%');
    }

    /**
     * @inheritDoc
     */
    public function getLabel(object $entity): string
    {
        return $entity->getName();
    }

    /**
     * @inheritDoc
     */
    public function getValue(object $entity): mixed
    {
        return $entity->getId();
    }

    /**
     * @inheritDoc
     */
    public function isGranted(Security $security): bool
    {
        return true;
    }
}