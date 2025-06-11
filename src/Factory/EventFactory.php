<?php

namespace App\Factory;

use App\Entity\Event;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Event>
 */
final class EventFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Event::class;
    }

    public function withName(string $name = 'Symfony'): self
    {
        return $this->with(['name' => $name.self::faker()->realText(30)]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $startAt = self::faker()->dateTimeBetween('01-03-2015', '01-04-2025');
        $endAt = self::faker()->dateTimeBetween(
            $startAt->add(new \DateInterval('P1D'))->format('d-m-Y'),
            $startAt->add(new \DateInterval('P1W'))->format('d-m-Y')
        );

        return [
            'name' => self::faker()->realText(50),
            'accessible' => self::faker()->boolean(),
            'description' => self::faker()->realText(),
            'startAt' => \DateTimeImmutable::createFromMutable($startAt),
            'endAt' => \DateTimeImmutable::createFromMutable($endAt),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Event $event): void {})
        ;
    }
}
