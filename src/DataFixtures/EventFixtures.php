<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public const SF_LIVE = 'sf_live_';

    public function load(ObjectManager $manager): void
    {
        for ($i = 15; $i <= 25; ++$i) {
            $year = '20'.$i;
            $event = (new Event())
                ->setName('SymfonyLive '.$year)
                ->setDescription('Share your best practices, experience and knowledge with Symfony.')
                ->setAccessible(true)
                ->setStartAt(new \DateTimeImmutable('28-03-'.$year))
                ->setEndAt(new \DateTimeImmutable('29-03-'.$year))
            ;

            foreach ((array) array_rand(TagFixtures::TAGS, rand(1, 3)) as $key) {
                $name = TagFixtures::TAGS[$key];
                $event->addTag($this->getReference(TagFixtures::TAG_NAME.$name, Tag::class));
            }

            $manager->persist($event);
            $manager->flush();
            $this->addReference(self::SF_LIVE.$i, $event);
        }
    }

    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
        ];
    }
}
