<?php

namespace App\Twig\Components;

use App\Entity\Organization;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

trait FormWithOrganizationsTrait
{
    /**
     * @var Organization[]
     */
    #[LiveProp(writable: true)]
    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    public array $organizations = [];

    #[ExposeInTemplate]
    public function getAllOrgs(): array
    {
        return $this->manager->getRepository(Organization::class)->findAll();
    }

    #[LiveListener('org:created')]
    public function onOrganizationCreated(#[LiveArg] Organization $organization): void
    {
        $this->organizations[] = $organization;
    }

    public function isOrgSelected(Organization $organization): bool
    {
        return \in_array($organization, $this->organizations, true);
    }
}