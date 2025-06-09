<?php

namespace App\Matching\Strategy;

use App\Entity\User;

interface MatchingStrategyInterface
{
    public function match(User $user): iterable;
}