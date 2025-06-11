<?php

namespace App\Matching\Strategy;

use App\Entity\Event;
use App\Entity\Volunteer;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;

class TagBasedStrategy implements MatchingStrategyInterface
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function match(Volunteer $volunteer): array
    {
        $volunteerProfile = $volunteer->getVolunteerProfile();
        if (!$volunteerProfile) {
            return [];
        }

        $volunteerTags = $volunteerProfile->getInterests();
        if ($volunteerTags->isEmpty()) {
            return [];
        }

        $tagIds = array_map(
            fn ($tag) => $tag->getId(),
            $volunteerTags->toArray()
        );

        // Find events that have any of the volunteer's interest tags
        $events = $this->eventRepository->findForTags($tagIds);

        // Sort events by number of matching tags (most matches first)
        usort($events, function (Event $a, Event $b) use ($tagIds) {
            $aTags = $a->getTags() ?? new ArrayCollection();
            $bTags = $b->getTags() ?? new ArrayCollection();

            $aMatchCount = count(array_intersect(
                $tagIds,
                array_map(fn ($tag) => $tag->getId(), $aTags->toArray())
            ));

            $bMatchCount = count(array_intersect(
                $tagIds,
                array_map(fn ($tag) => $tag->getId(), $bTags->toArray())
            ));

            return $bMatchCount <=> $aMatchCount;
        });

        return $events;
    }

    public function getName(): string
    {
        return 'tag_based';
    }

    public function getDescription(): string
    {
        return 'Matches events based on tags that match your interests, prioritizing those with the most tag matches.';
    }
}
