<?php

namespace App\Matching\Strategy;

use App\Entity\Volunteer;
use App\Repository\VolunteerRepository;
use Doctrine\Common\Collections\Collection;

class SkillBasedStrategy implements MatchingStrategyInterface
{
    public function __construct(
        private readonly VolunteerRepository $volunteerRepository,
    ) {
    }

    public function match(Volunteer $volunteer): array
    {
        $volunteerSkills = $volunteer->getVolunteerProfile()?->getSkills() ?? new Collection();

        if ($volunteerSkills->isEmpty()) {
            return [];
        }

        $skillIds = array_map(
            fn ($skill) => $skill->getId(),
            $volunteerSkills->toArray()
        );

        $matches = $this->volunteerRepository->findBySharedSkills($skillIds, $volunteer->getId());

        // Sort matches by number of shared skills
        usort($matches, function (Volunteer $a, Volunteer $b) use ($skillIds) {
            $aSkills = $a->getVolunteerProfile()?->getSkills() ?? new Collection();
            $bSkills = $b->getVolunteerProfile()?->getSkills() ?? new Collection();

            $aSharedCount = count(array_intersect(
                $skillIds,
                array_map(fn ($skill) => $skill->getId(), $aSkills->toArray())
            ));

            $bSharedCount = count(array_intersect(
                $skillIds,
                array_map(fn ($skill) => $skill->getId(), $bSkills->toArray())
            ));

            return $bSharedCount <=> $aSharedCount;
        });

        return $matches;
    }

    public function getName(): string
    {
        return 'skill_based';
    }

    public function getDescription(): string
    {
        return 'Matches volunteers based on shared skills, prioritizing those with the most skills in common.';
    }
}
