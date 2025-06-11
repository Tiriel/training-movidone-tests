<?php

namespace App\Matching;

use App\Entity\Event;
use App\Entity\User;
use App\Matching\Strategy\MatchingStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class VolunteerMatcher
{
    /**
     * @var array<string, MatchingStrategyInterface>
     */
    private array $strategies = [];

    /**
     * @param iterable<MatchingStrategyInterface> $strategies
     */
    public function __construct(
        #[TaggedIterator('app.matching_strategy')]
        iterable $strategies,
    ) {
        foreach ($strategies as $strategy) {
            $this->addStrategy($strategy);
        }
    }

    /**
     * Add a matching strategy.
     */
    public function addStrategy(MatchingStrategyInterface $strategy): void
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * Get all available strategies.
     *
     * @return array<string, MatchingStrategyInterface>
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }

    /**
     * Find matches using a specific strategy.
     *
     * @return array<Event>
     */
    public function findMatches(User $user, string $strategyName): array
    {
        if (!isset($this->strategies[$strategyName])) {
            throw new \InvalidArgumentException(sprintf('Strategy "%s" not found. Available strategies: %s', $strategyName, implode(', ', array_keys($this->strategies))));
        }

        return $this->strategies[$strategyName]->match($user);
    }

    /**
     * Find matches using all available strategies.
     *
     * @return array<string, array<Event>>
     */
    public function findMatchesUsingAllStrategies(User $user): array
    {
        $results = [];
        foreach ($this->strategies as $name => $strategy) {
            $results[$name] = $strategy->match($user);
        }

        return $results;
    }
}
