<?php

namespace App\Matching\Strategy;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.matching_strategy')]
interface MatchingStrategyInterface
{
    /**
     * Returns matching volunteers for the given volunteer.
     *
     * @param User $user The volunteer to find matches for
     *
     * @return array<User> Array of matching volunteers sorted by relevance
     */
    public function match(User $user): array;

    /**
     * Returns the name of the strategy.
     */
    public function getName(): string;

    /**
     * Returns a description of how the strategy works.
     */
    public function getDescription(): string;
}
