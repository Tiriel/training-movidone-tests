<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SkillFixtures extends Fixture
{
    public const SKILL_NAME = 'skill_';
    public const SKILLS = [
        'First Aid',
        'Catering',
        'Reception',
        'Animation',
        'Heavy loading',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::SKILLS as $name) {
            $tag = (new Skill())->setName($name);
            $manager->persist($tag);
            $manager->flush();

            $this->addReference(self::SKILL_NAME.$name, $tag);
        }
    }
}
