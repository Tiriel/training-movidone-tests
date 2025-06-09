<?php

namespace App\Matching\Strategy;

use App\Entity\User;
use App\Matching\Strategy\MatchingStrategyInterface;

class LocationBasedStrategy implements MatchingStrategyInterface
{

    public function match(User $user): iterable
    {
        // TODO: Implement match() method.
        return [];
    }
}