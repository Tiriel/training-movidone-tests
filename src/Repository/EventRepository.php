<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Skill;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findEventsBetweenDates(?\DateTimeImmutable $start = null, ?\DateTimeImmutable $end = null): array
    {
        if (null === $start && null === $end) {
            throw new \InvalidArgumentException('At least one date is required to operate this method.');
        }

        $qb = $this->createQueryBuilder('e');

        if ($start instanceof \DateTimeImmutable) {
            $qb->andWhere('e.startAt >= :start')
                ->setParameter('start', $start);
        }

        if ($end instanceof \DateTimeImmutable) {
            $qb->andWhere('e.endAt <= :end')
                ->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();
    }

    public function findLikeName(string $name): array
    {
        $qb = $this->createQueryBuilder('e');

        return $qb->andWhere($qb->expr()->like('e.name', ':name'))
            ->setParameter('name', sprintf('%%%s%%', $name))
            ->getQuery()
            ->getResult();
    }

    public function findForTags(User $user): iterable
    {
        $qb = $this->createQueryBuilder('e');
        $tagIds = $user
            ->getVolunteerProfile()
            ->getInterests()
            ->map(fn (Tag $tag) => $tag->getId());

        return $qb
            ->innerJoin('e.tags', 't')
            ->where($qb->expr()->in('t.id', ':tagIds'))
            ->setParameter('tagIds', $tagIds)
            ->groupBy('e.id')
            ->orderBy($qb->expr()->count('t.id'), 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findForSkills(User $user): iterable
    {
        $qb = $this->createQueryBuilder('e');
        $skillIds = $user
            ->getVolunteerProfile()
            ->getSkills()
            ->map(fn (Skill $skill) => $skill->getId());

        return $qb
            ->innerJoin('e.neededSkills', 's')
            ->where($qb->expr()->in('s.id', ':skillIds'))
            ->setParameter('skillIds', $skillIds)
            ->groupBy('e.id')
            ->orderBy($qb->expr()->count('s.id'), 'DESC')
            ->getQuery()
            ->getResult();
    }
}
