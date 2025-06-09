<?php

namespace App\Matching\Strategy;

use App\Entity\Volunteer;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.matching_strategy')]
interface MatchingStrategyInterface
{
    /**
     * Returns matching volunteers for the given volunteer.
     *
     * @param Volunteer $volunteer The volunteer to find matches for
     *
     * @return array<Volunteer> Array of matching volunteers sorted by relevance
     */
    public function match(Volunteer $volunteer): array;

    /**
     * Returns the name of the strategy.
     */
    public function getName(): string;

    /**
     * Returns a description of how the strategy works.
     */
    public function getDescription(): string;
}
