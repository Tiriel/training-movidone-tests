<?php

namespace App\Tests\Smoke;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Zenstruck\Foundry\Test\Factories;

class SmokeTest extends WebTestCase
{
    use Factories;

    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        $user = UserFactory::createOne();

        static::$client = static::createClient();
        static::$client->loginUser($user->_real());
    }

    /**
     * @dataProvider provideMethodAndStaticUrl
     * @group smoke
     */
    public function testPublicUrlIsSuccessful(string $method, string $url): void
    {
        static::$client->request($method, $url);
        if (\in_array(static::$client->getResponse()->getStatusCode(), [301, 302, 307, 308])) {
            static::$client->followRedirect();
        }

        $this->assertSame(200, static::$client->getResponse()->getStatusCode());
    }

    public function provideMethodAndStaticUrl(): \Generator
    {
        /** @var RouterInterface $router */
        $router = static::getContainer()->get(RouterInterface::class);
        $collection = $router->getRouteCollection();
        static::ensureKernelShutdown();

        foreach ($collection as $routeName => $route) {
            /** @var Route $route */
            $variable = $route->compile()->getVariables();
            if (count(array_diff($variable, array_keys($route->getDefaults()))) > 0) {
                continue;
            }
            if ([] === $methods = $route->getMethods()) {
                $methods[] = 'GET';
            }

            foreach ($methods as $method) {
                yield "$method $routeName" => [$method, $router->generate($routeName)];
            }
        }
    }
}
