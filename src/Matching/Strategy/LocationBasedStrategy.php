<?php

namespace App\Matching\Strategy;

use App\Entity\Volunteer;
use App\Repository\VolunteerRepository;

class LocationBasedStrategy implements MatchingStrategyInterface
{
    private const MAX_DISTANCE_KM = 50; // Maximum distance in kilometers for matching

    public function __construct(
        private readonly VolunteerRepository $volunteerRepository,
    ) {
    }

    public function match(Volunteer $volunteer): array
    {
        $profile = $volunteer->getVolunteerProfile();
        if (!$profile || !$profile->getLatitude() || !$profile->getLongitude()) {
            return [];
        }

        $matches = $this->volunteerRepository->findByLocation(
            $profile->getLatitude(),
            $profile->getLongitude(),
            self::MAX_DISTANCE_KM,
            $volunteer->getId()
        );

        // Sort matches by distance
        usort($matches, function (Volunteer $a, Volunteer $b) use ($profile) {
            $aProfile = $a->getVolunteerProfile();
            $bProfile = $b->getVolunteerProfile();

            if (!$aProfile || !$bProfile) {
                return 0;
            }

            $distanceA = $this->calculateDistance(
                $profile->getLatitude(),
                $profile->getLongitude(),
                $aProfile->getLatitude(),
                $aProfile->getLongitude()
            );

            $distanceB = $this->calculateDistance(
                $profile->getLatitude(),
                $profile->getLongitude(),
                $bProfile->getLatitude(),
                $bProfile->getLongitude()
            );

            return $distanceA <=> $distanceB;
        });

        return $matches;
    }

    public function getName(): string
    {
        return 'location_based';
    }

    public function getDescription(): string
    {
        return sprintf('Matches volunteers within %d kilometers, sorted by proximity.', self::MAX_DISTANCE_KM);
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
