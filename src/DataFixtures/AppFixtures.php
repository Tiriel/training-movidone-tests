<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\VolunteerProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        protected readonly UserPasswordHasherInterface $hasher,
        #[Autowire(param: 'env(ADMIN_PWD)')]
        protected readonly string $adminPwd,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $profile = new VolunteerProfile();

        foreach ((array) array_rand(SkillFixtures::SKILLS, rand(2, 4)) as $key) {
            $name = SkillFixtures::SKILLS[$key];
            $profile->addSkill($this->getReference(SkillFixtures::SKILL_NAME.$name, Skill::class));
        }


        foreach ((array) array_rand(TagFixtures::TAGS, rand(2, 4)) as $key) {
            $name = TagFixtures::TAGS[$key];
            $profile->addInterest($this->getReference(TagFixtures::TAG_NAME.$name, Tag::class));
        }

        $user = (new User())
            ->setEmail('benjamin.zaslavsky@gmail.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setApiKey()
        ;
        $user->setPassword($this->hasher->hashPassword($user, $this->adminPwd));
        $user->setVolunteerProfile($profile);

        $manager->persist($profile);
        $manager->persist($user);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SkillFixtures::class,
            TagFixtures::class,
        ];
    }
}
