<?php

namespace App\Matching\Strategy;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;

class SkillBasedStrategy implements MatchingStrategyInterface
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function match(User $user): array
    {
        $volunteerProfile = $user->getVolunteerProfile();
        if (!$volunteerProfile) {
            return [];
        }

        $volunteerSkills = $volunteerProfile->getSkills();
        if ($volunteerSkills->isEmpty()) {
            return [];
        }

        $skillIds = array_map(
            fn ($skill) => $skill->getId(),
            $volunteerSkills->toArray()
        );

        // Find events that require any of the volunteer's skills
        $events = $this->eventRepository->findForSkills($skillIds);

        // Sort events by number of matching skills (most matches first)
        usort($events, function (Event $a, Event $b) use ($skillIds) {
            $aSkills = $a->getNeededSkills() ?? new ArrayCollection();
            $bSkills = $b->getNeededSkills() ?? new ArrayCollection();

            $aMatchCount = count(array_intersect(
                $skillIds,
                array_map(fn ($skill) => $skill->getId(), $aSkills->toArray())
            ));

            $bMatchCount = count(array_intersect(
                $skillIds,
                array_map(fn ($skill) => $skill->getId(), $bSkills->toArray())
            ));

            return $bMatchCount <=> $aMatchCount;
        });

        return $events;
    }

    public function getName(): string
    {
        return 'skill_based';
    }

    public function getDescription(): string
    {
        return 'Matches events based on needed skills that match your skills, prioritizing those with the most skill matches.';
    }
}
