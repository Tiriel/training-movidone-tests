<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Event;
use App\Factory\EventFactory;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class EventRepositoryTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    protected function setUp(): void
    {
        $fixtures = [];

        for ($i = 15; $i <= 25; $i++) {
            $fixtures[] = [
                'name' => 'Symfony Live 20'.$i,
                'startAt' => new \DateTimeImmutable('28-03-20'.$i),
                'endAt' => new \DateTimeImmutable('29-03-20'.$i),
            ];
        }
        EventFactory::createSequence($fixtures);
    }

    /**
     * @dataProvider provideYearCountAndDates
     * @group integration
     */
    public function testFindEventBetweenDatesReturnsAllEventsBetweenDates(
        int $count,
        ?string $lastYear,
        ?\DateTimeImmutable $start = null,
        ?\DateTimeImmutable $end = null
    ): void {
        if (null === $start && null === $end) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('At least one date is required to operate this method.');
        }

        $events = static::getContainer()->get(EventRepository::class)->findEventsBetweenDates($start, $end);

        $lastEvent = end($events) ?: null;

        $this->assertCount($count, $events);
        $this->assertSame($lastYear, $lastEvent?->getStartAt()?->format('Y'));
    }

    public static function provideYearCountAndDates(): iterable
    {
        yield 'no date' => [0, ''];
        yield 'not found' => [0, null, new \DateTimeImmutable('01-03-2026')];
        yield 'after 2020' => [6, '2025', new \DateTimeImmutable('01-03-2020')];
        yield 'before 2020' => [5, '2019', null, new \DateTimeImmutable('01-03-2020')];
        yield 'between 2020-2021' => [2, '2021', new \DateTimeImmutable('01-03-2020'), new \DateTimeImmutable('31-03-2021')];
    }
}
