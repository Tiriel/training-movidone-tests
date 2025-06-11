<?php

namespace App\Tests\Functional\Controller;

use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Symfony\Component\Panther\PantherTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class EventControllerTest extends PantherTestCase
{
    use Factories, ResetDatabase, HasBrowser;

    public function testSearchEventPerformsSearchAndDisplaysResult(): void
    {
        $user = UserFactory::createOne();
        EventFactory::createMany(10);
        self::ensureKernelShutdown();

        $this->pantherBrowser()
            ->visit('/events')
            ->fillField('Email', $user->getUserIdentifier())
            ->fillField('Password', 'admin1234!')
            ->click('Sign in')
            ->assertOn('/events')
            ->assertSee('List all events')
            ->fillField('name', 'SymfonyLive')
            ->click('Search')
            ->assertOn('/events/search?name=SymfonyLive')
            ->takeScreenshot('search_event.jpg')
        ;
    }
}
