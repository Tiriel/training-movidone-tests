<?php

namespace App\Tests\Functional\Controller;

use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class VolunteerControllerTest extends WebTestCase
{
    use Factories, ResetDatabase, HasBrowser;

    public function testNewVolunteerForm(): void
    {
        $user = UserFactory::createOne();
        EventFactory::createOne([
            'startAt' => new \DateTimeImmutable('2019-01-01'),
            'endAt' => new \DateTimeImmutable('2019-01-02'),
        ]);
        self::ensureKernelShutdown();

        $this->browser()
            ->actingAs($user)
            ->visit('/volunteer/new?event=1')
            ->assertSee('New volunteer')
            ->fillField('Start at', '2019-01-01')
            ->fillField('End at', '2019-01-02')
            ->click('Save')
            ->assertOn('/volunteer/1')
            ->assertNotSee('You have to select and event or a project, or both');

    }
}
