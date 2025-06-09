<?php

namespace App\Twig\Components;

use App\Entity\VolunteerProfile;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Address
{
    public ?VolunteerProfile $profile = null;

    public bool $showEditLink = false;

    public function getFormattedAddress(): ?string
    {
        if (!$this->profile) {
            return null;
        }

        $parts = array_filter([
            $this->profile->getAddress(),
            $this->profile->getPostalCode(),
            $this->profile->getCity(),
            $this->profile->getCountry()
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    public function hasAddress(): bool
    {
        return $this->profile && (
            $this->profile->getAddress() ||
            $this->profile->getCity() ||
            $this->profile->getCountry()
        );
    }

    public function hasCoordinates(): bool
    {
        return $this->profile &&
            $this->profile->getLatitude() !== null &&
            $this->profile->getLongitude() !== null;
    }

    public function getGoogleMapsUrl(): ?string
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return sprintf(
            'https://www.google.com/maps?q=%f,%f',
            $this->profile->getLatitude(),
            $this->profile->getLongitude()
        );
    }
}
