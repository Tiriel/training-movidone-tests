<?php

namespace App\Tests\Unit\Parser;

use App\Entity\Event;
use App\Entity\Organization;
use App\Parser\ApiResultParser;
use App\Repository\EventRepository;
use App\Repository\OrganizationRepository;
use App\Transformer\ApiToEntityTransformerInterface;
use App\Transformer\ApiToEventTransformer;
use App\Transformer\ApiToOrganizationTransformer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ApiResultParserTest extends TestCase
{
    public function testParserCreatesObjectsForNonAdmin(): void
    {
        $event = (new Event())->setName('Test Event');
        $organization = (new Organization())->setName('Test Organization');

        $parser = new ApiResultParser(
            $this->getMockEntityManager(),
            $this->getMockTransformer(ApiToEventTransformer::class, $event, true),
            $this->getMockTransformer(ApiToOrganizationTransformer::class, $organization, true),
            $this->getMockAuthorizationChecker(false)
        );

        $result = $parser->parseResults([
            [
                'name' => 'Test Event',
                'startDate' => '2018-01-01',
                'organizations' => [['name' => 'Test Organization']]
            ]
        ]);

        $this->assertSame([$event], $result);
        $this->assertSame([$organization], $result[0]->getOrganizations()->toArray());
    }

    public function testParserCreatesAndPersistsObjectsForAdmin(): void
    {
        $event = (new Event())->setName('Test Event');
        $organization = (new Organization())->setName('Test Organization');

        $parser = new ApiResultParser(
            $this->getMockEntityManager(persist:  true),
            $this->getMockTransformer(ApiToEventTransformer::class, $event, true),
            $this->getMockTransformer(ApiToOrganizationTransformer::class, $organization, true),
            $this->getMockAuthorizationChecker(true)
        );

        $result = $parser->parseResults([
            [
                'name' => 'Test Event',
                'startDate' => '2018-01-01',
                'organizations' => [['name' => 'Test Organization']]
            ]
        ]);

        $this->assertSame([$event], $result);
        $this->assertSame([$organization], $result[0]->getOrganizations()->toArray());
    }

    public function testParserFindsEventButNotOrg(): void
    {
        $event = (new Event())->setName('Test Event');
        $organization = (new Organization())->setName('Test Organization');

        $parser = new ApiResultParser(
            $this->getMockEntityManager($event),
            $this->getMockTransformer(ApiToEventTransformer::class),
            $this->getMockTransformer(ApiToOrganizationTransformer::class, $organization, true),
            $this->getMockAuthorizationChecker(false)
        );

        $result = $parser->parseResults([
            [
                'name' => 'Test Event',
                'startDate' => '2018-01-01',
                'organizations' => [['name' => 'Test Organization']]
            ]
        ]);

        $this->assertSame([$event], $result);
        $this->assertSame([$organization], $result[0]->getOrganizations()->toArray());
    }

    public function testParserFindsOrgButNotEvent(): void
    {
        $event = (new Event())->setName('Test Event');
        $organization = (new Organization())->setName('Test Organization');

        $parser = new ApiResultParser(
            $this->getMockEntityManager(null, $organization),
            $this->getMockTransformer(ApiToEventTransformer::class, $event, true),
            $this->getMockTransformer(ApiToOrganizationTransformer::class),
            $this->getMockAuthorizationChecker(false)
        );

        $result = $parser->parseResults([
            [
                'name' => 'Test Event',
                'startDate' => '2018-01-01',
                'organizations' => [['name' => 'Test Organization']]
            ]
        ]);

        $this->assertSame([$event], $result);
        $this->assertSame([$organization], $result[0]->getOrganizations()->toArray());
    }

    public function testParserFindsEventAndOrgObjectForAdmin(): void
    {
        $event = (new Event())->setName('Test Event');
        $organization = (new Organization())->setName('Test Organization');

        $parser = new ApiResultParser(
            $this->getMockEntityManager($event, $organization),
            $this->getMockTransformer(ApiToEventTransformer::class),
            $this->getMockTransformer(ApiToOrganizationTransformer::class),
            $this->getMockAuthorizationChecker(false)
        );

        $result = $parser->parseResults([
            [
                'name' => 'Test Event',
                'startDate' => '2018-01-01',
                'organizations' => [['name' => 'Test Organization']]
            ]
        ]);

        $this->assertSame([$event], $result);
        $this->assertSame([$organization], $result[0]->getOrganizations()->toArray());
    }

    private function getMockEntityManager(?object $eventResult = null, ?object $orgResult = null, bool $persist = false): MockObject&EntityManagerInterface
    {
        $mock = $this->createMock(EntityManagerInterface::class);
        $mock->expects($this->atLeast(2))
            ->method('getRepository')
            ->willReturn(
                $this->getMockRepository(EventRepository::class, $eventResult),
                $this->getMockRepository(OrganizationRepository::class, $orgResult)
            );

        $mock->expects(
            $persist
                ? $this->atLeast(1)
                : $this->never()
        )
            ->method('persist');

        return $mock;
    }

    private function getMockRepository(string $classname, ?object $result = null): MockObject&ServiceEntityRepository
    {
        /** @var class-string|ServiceEntityRepository $classname */
        if (!is_subclass_of($classname, ServiceEntityRepository::class)) {
            throw new \InvalidArgumentException(
                sprintf('Argument "$classname" must be an instance of "%s"', ServiceEntityRepository::class)
            );
        }

        /** @var MockObject&ServiceEntityRepository $mock */
        $mock = $this->createMock($classname);
        $mock->expects($this->atLeastOnce())
            ->method('findOneBy')
            ->willReturn($result);

        return $mock;
    }

    private function getMockTransformer(string $classname, ?object $result = null, bool $called = false): MockObject&ApiToEntityTransformerInterface
    {
        /** @var class-string|ApiToEntityTransformerInterface $classname */
        if (!is_subclass_of($classname, ApiToEntityTransformerInterface::class)) {
            throw new \InvalidArgumentException(
                sprintf('Argument "$classname" must be an instance of "%s"', ApiToEntityTransformerInterface::class)
            );
        }

        /** @var MockObject&ApiToEntityTransformerInterface $mock */
        $mock = $this->createMock($classname);

        if ($called) {
            $mock->expects($this->atLeastOnce())
                ->method('transform')
                ->willReturn($result);
        }

        return $mock;
    }

    private function getMockAuthorizationChecker(bool $isAdmin): MockObject&AuthorizationCheckerInterface
    {
        $mock = $this->createMock(AuthorizationCheckerInterface::class);
        $mock->expects($this->atLeastOnce())
            ->method('isGranted')
            ->willReturn($isAdmin);

        return $mock;
    }
}
