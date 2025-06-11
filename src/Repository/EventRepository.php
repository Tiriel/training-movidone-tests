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

    /**
     * Find events that have any of the given tags.
     *
     * @param array<int> $tagIds Array of tag IDs to match against
     * @return array<Event>
     */
    public function findForTags(array $tagIds): array
    {
        $qb = $this->createQueryBuilder('e');

        return $qb
            ->innerJoin('e.tags', 't')
            ->where($qb->expr()->in('t.id', ':tagIds'))
            ->setParameter('tagIds', $tagIds)
            ->groupBy('e.id')
            ->orderBy($qb->expr()->count('t.id'), 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events that require any of the given skills.
     *
     * @param array<int> $skillIds Array of skill IDs to match against
     * @return array<Event>
     */
    public function findForSkills(array $skillIds): array
    {
        $qb = $this->createQueryBuilder('e');

        return $qb
            ->innerJoin('e.neededSkills', 's')
            ->where($qb->expr()->in('s.id', ':skillIds'))
            ->setParameter('skillIds', $skillIds)
            ->groupBy('e.id')
            ->orderBy($qb->expr()->count('s.id'), 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events within a certain distance of a location.
     *
     * @param float $latitude The latitude of the center point
     * @param float $longitude The longitude of the center point
     * @param float $maxDistanceKm The maximum distance in kilometers
     * @return array<Event>
     */
    public function findByLocation(float $latitude, float $longitude, float $maxDistanceKm): array
    {
        // Using the Haversine formula in SQL to calculate distances
        $sql = '
            SELECT e.*, 
                   (6371 * acos(cos(radians(:lat)) * cos(radians(e.latitude)) 
                   * cos(radians(e.longitude) - radians(:lon)) 
                   + sin(radians(:lat)) * sin(radians(e.latitude)))) AS distance
            FROM event e
            WHERE e.latitude IS NOT NULL 
              AND e.longitude IS NOT NULL
              AND (6371 * acos(cos(radians(:lat)) * cos(radians(e.latitude)) 
                   * cos(radians(e.longitude) - radians(:lon)) 
                   + sin(radians(:lat)) * sin(radians(e.latitude)))) <= :dist
            ORDER BY distance ASC
        ';

        $em = $this->getEntityManager();
        $query = $em->createNativeQuery($sql, $this->createResultSetMappingForEvent());
        $query->setParameters([
            'lat' => $latitude,
            'lon' => $longitude,
            'dist' => $maxDistanceKm,
        ]);

        return $query->getResult();
    }

    /**
     * Create a ResultSetMapping for the Event entity.
     */
    private function createResultSetMappingForEvent(): \Doctrine\ORM\Query\ResultSetMapping
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult(Event::class, 'e');
        
        // Map all fields from the Event entity
        $rsm->addFieldResult('e', 'id', 'id');
        $rsm->addFieldResult('e', 'name', 'name');
        $rsm->addFieldResult('e', 'description', 'description');
        $rsm->addFieldResult('e', 'accessible', 'accessible');
        $rsm->addFieldResult('e', 'prerequisites', 'prerequisites');
        $rsm->addFieldResult('e', 'start_at', 'startAt');
        $rsm->addFieldResult('e', 'end_at', 'endAt');
        $rsm->addFieldResult('e', 'address', 'address');
        $rsm->addFieldResult('e', 'latitude', 'latitude');
        $rsm->addFieldResult('e', 'longitude', 'longitude');
        $rsm->addFieldResult('e', 'postal_code', 'postalCode');
        $rsm->addFieldResult('e', 'city', 'city');
        $rsm->addFieldResult('e', 'country', 'country');

        return $rsm;
    }
}
