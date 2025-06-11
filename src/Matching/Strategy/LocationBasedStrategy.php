<?php

namespace App\Matching\Strategy;

use App\Entity\Event;
use App\Entity\Volunteer;
use App\Repository\EventRepository;

class LocationBasedStrategy implements MatchingStrategyInterface
{
    private const MAX_DISTANCE_KM = 50; // Maximum distance in kilometers for matching

    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function match(Volunteer $volunteer): array
    {
        $profile = $volunteer->getVolunteerProfile();
        if (!$profile || !$profile->getLatitude() || !$profile->getLongitude()) {
            return [];
        }

        $events = $this->eventRepository->findByLocation(
            $profile->getLatitude(),
            $profile->getLongitude(),
            self::MAX_DISTANCE_KM
        );

        // Sort events by distance
        usort($events, function (Event $a, Event $b) use ($profile) {
            if (!$a->getLatitude() || !$a->getLongitude() || !$b->getLatitude() || !$b->getLongitude()) {
                return 0;
            }

            $distanceA = $this->calculateDistance(
                $profile->getLatitude(),
                $profile->getLongitude(),
                $a->getLatitude(),
                $a->getLongitude()
            );

            $distanceB = $this->calculateDistance(
                $profile->getLatitude(),
                $profile->getLongitude(),
                $b->getLatitude(),
                $b->getLongitude()
            );

            return $distanceA <=> $distanceB;
        });

        return $events;
    }

    public function getName(): string
    {
        return 'location_based';
    }

    public function getDescription(): string
    {
        return sprintf('Matches events within %d kilometers of your location, sorted by proximity.', self::MAX_DISTANCE_KM);
    }

    /**
     * Calculate distance between two points using the Haversine formula.
     */
    private function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2,
    ): float {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
