<?php

namespace App\Matching\Strategy;

use App\Entity\Volunteer;
use App\Repository\VolunteerRepository;
use Doctrine\Common\Collections\Collection;

class TagBasedStrategy implements MatchingStrategyInterface
{
    public function __construct(
        private readonly VolunteerRepository $volunteerRepository,
    ) {
    }

    public function match(Volunteer $volunteer): array
    {
        $volunteerTags = $volunteer->getVolunteerProfile()?->getTags() ?? new Collection();

        if ($volunteerTags->isEmpty()) {
            return [];
        }

        $tagIds = array_map(
            fn ($tag) => $tag->getId(),
            $volunteerTags->toArray()
        );

        $matches = $this->volunteerRepository->findBySharedTags($tagIds, $volunteer->getId());

        // Sort matches by number of shared tags
        usort($matches, function (Volunteer $a, Volunteer $b) use ($tagIds) {
            $aTags = $a->getVolunteerProfile()?->getTags() ?? new Collection();
            $bTags = $b->getVolunteerProfile()?->getTags() ?? new Collection();

            $aSharedCount = count(array_intersect(
                $tagIds,
                array_map(fn ($tag) => $tag->getId(), $aTags->toArray())
            ));

            $bSharedCount = count(array_intersect(
                $tagIds,
                array_map(fn ($tag) => $tag->getId(), $bTags->toArray())
            ));

            return $bSharedCount <=> $aSharedCount;
        });

        return $matches;
    }

    public function getName(): string
    {
        return 'tag_based';
    }

    public function getDescription(): string
    {
        return 'Matches volunteers based on shared interest tags, prioritizing those with the most tags in common.';
    }
}
