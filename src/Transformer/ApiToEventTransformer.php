<?php

namespace App\Transformer;

use App\Entity\Event;

class ApiToEventTransformer implements ApiToEntityTransformerInterface
{
    private const KEYS = [
        'name',
        'description',
        'accessible',
        'startDate',
        'endDate',
    ];

    public function transform(array $data): Event
    {
        if (0 < \count(\array_diff(self::KEYS, \array_keys($data)))) {
            throw new \RuntimeException("Missing keys in data provided by the API");
        }

        return (new Event())
            ->setName($data['name'])
            ->setStartAt(new \DateTimeImmutable($data['startDate']))
            ->setEndAt(new \DateTimeImmutable($data['endDate']))
            ->setDescription($data['description'])
            ->setAccessible($data['accessible']);
    }
}
