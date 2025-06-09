<?php

namespace App\Matching\Strategy;

use App\Entity\User;
use App\Repository\EventRepository;

class TagBasedStrategy implements MatchingStrategyInterface
{
    public function __construct(
        private readonly EventRepository $repository,
    ) {}

    public function match(User $user): iterable
    {
        return $this->repository->findForTags($user);
    }
}