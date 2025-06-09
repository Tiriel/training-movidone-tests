<?php

namespace App\Autocompleter;

use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'project'])]
class ProjectAutocompleter implements EntityAutocompleterInterface
{
    public function getEntityClass(): string
    {
        return Project::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository->createQueryBuilder('p')
            ->where('p.name LIKE :query')
            ->setParameter('query', '%'.$query.'%');
    }

    public function getLabel(object $entity): string
    {
        return $entity->getName();
    }

    public function getValue(object $entity): mixed
    {
        return $entity->getId();
    }

    public function isGranted(Security $security): bool
    {
        return true;
    }
}
