<?php

namespace App\Tests\Integration\Search;

use App\Factory\EventFactory;
use App\Search\DatabaseEventSearch;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DatabaseEventSearchTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
    }

    public function testSearchByNameReturnsEventWithMatchingName(): void
    {
        EventFactory::createMany(20);
        EventFactory::new()->withName('Symfony')->many(2)->create();

        /** @var DatabaseEventSearch $search */
        $search = static::getContainer()->get(DatabaseEventSearch::class);
        $events = $search->searchByName('Symfony');

        $this->assertCount(2, $events);
    }

    public function testSearchByNameReturnsAllEventsWithoutName(): void
    {
        EventFactory::createMany(20);
        EventFactory::new()->withName('Symfony')->many(2)->create();

        /** @var DatabaseEventSearch $search */
        $search = static::getContainer()->get(DatabaseEventSearch::class);
        $events = $search->searchByName();

        $this->assertCount(22, $events);
    }
}
