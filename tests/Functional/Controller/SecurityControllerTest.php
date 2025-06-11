<?php

namespace App\Tests\Functional\Controller;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends WebTestCase
{
    use Factories, ResetDatabase, HasBrowser;

    public function testLoginIsSuccessful(): void
    {
        $user = UserFactory::createOne();
        self::ensureKernelShutdown();

        $this->browser()
            ->visit('/login')
            ->fillField('Email', $user->getUserIdentifier())
            ->fillField('Password', 'admin1234!')
            ->click('Sign in')
            ->assertOn('/');
    }

    public function testLoginIsFailed(): void
    {
        $user = UserFactory::createOne();
        self::ensureKernelShutdown();

        $this->browser()
            ->visit('/login')
            ->fillField('Email', 'wrong@email.com')
            ->fillField('Password', 'admin1234!')
            ->click('Sign in')
            ->assertOn('/login')
            ->assertSeeElement('div.alert.alert-danger')
        ;
    }
}
