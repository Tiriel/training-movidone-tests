<?php

namespace App\Matching\Strategy;

use App\Entity\User;
use App\Matching\Strategy\MatchingStrategyInterface;
use App\Repository\EventRepository;

class SkillBasedStrategy implements MatchingStrategyInterface
{
    public function __construct(
        private readonly EventRepository $repository,
    ) {}

    public function match(User $user): iterable
    {
        return $this->repository->findForSkills($user);
    }
}