<?php

namespace App\Geocoding;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(NOMINATIM_URL)%')]
        private readonly string $nominatimUrl,
    ) {
    }

    /**
     * Geocode an address to get its coordinates.
     *
     * @return array{latitude: ?float, longitude: ?float, display_name: ?string}
     */
    public function geocodeAddress(
        string $address,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $country = null,
    ): array {
        $query = array_filter([
            $address,
            $postalCode,
            $city,
            $country,
        ]);

        $response = $this->httpClient->request('GET', $this->nominatimUrl, [
            'query' => [
                'q' => implode(', ', $query),
                'format' => 'json',
                'limit' => 1,
            ],
            'headers' => [
                'User-Agent' => 'VolunteerMatchingApp/1.0',
            ],
        ]);

        $data = $response->toArray();

        if (empty($data)) {
            return [
                'latitude' => null,
                'longitude' => null,
                'display_name' => null,
            ];
        }

        $result = $data[0];

        return [
            'latitude' => (float) $result['lat'],
            'longitude' => (float) $result['lon'],
            'display_name' => $result['display_name'],
        ];
    }
}
