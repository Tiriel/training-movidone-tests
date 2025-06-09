<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public const TAG_NAME = 'tag_';
    public const TAGS = [
        'Symfony',
        'PHP',
        'JavaScript',
        'Vue.js',
        'React.js',
        'DevOps',
        'Backend',
        'Frontend',
        'Python',
        'Django',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TAGS as $name) {
            $tag = (new Tag())->setName($name);
            $manager->persist($tag);
            $manager->flush();

            $this->addReference(self::TAG_NAME.$name, $tag);
        }
    }
}
