<?php

namespace App\Repository;

use App\Entity\Volunteer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Volunteer>
 *
 * @method Volunteer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Volunteer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Volunteer[]    findAll()
 * @method Volunteer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolunteerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volunteer::class);
    }

    /**
     * Find volunteers that share any of the given skills, excluding the given volunteer.
     *
     * @param array<int> $skillIds           Array of skill IDs to match against
     * @param int        $excludeVolunteerId Volunteer ID to exclude from results
     *
     * @return array<Volunteer>
     */
    public function findBySharedSkills(array $skillIds, int $excludeVolunteerId): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.volunteerProfile', 'vp')
            ->join('vp.skills', 's')
            ->where('s.id IN (:skillIds)')
            ->andWhere('v.id != :excludeId')
            ->setParameter('skillIds', $skillIds)
            ->setParameter('excludeId', $excludeVolunteerId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find volunteers that share any of the given tags, excluding the given volunteer.
     *
     * @param array<int> $tagIds             Array of tag IDs to match against
     * @param int        $excludeVolunteerId Volunteer ID to exclude from results
     *
     * @return array<Volunteer>
     */
    public function findBySharedTags(array $tagIds, int $excludeVolunteerId): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.volunteerProfile', 'vp')
            ->join('vp.tags', 't')
            ->where('t.id IN (:tagIds)')
            ->andWhere('v.id != :excludeId')
            ->setParameter('tagIds', $tagIds)
            ->setParameter('excludeId', $excludeVolunteerId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find volunteers within a certain distance, excluding the given volunteer
     * Uses the Haversine formula in SQL to calculate distances.
     *
     * @param float $latitude           The reference latitude
     * @param float $longitude          The reference longitude
     * @param float $maxDistanceKm      Maximum distance in kilometers
     * @param int   $excludeVolunteerId Volunteer ID to exclude from results
     *
     * @return array<Volunteer>
     */
    public function findByLocation(
        float $latitude,
        float $longitude,
        float $maxDistanceKm,
        int $excludeVolunteerId,
    ): array {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dql = <<<DQL
            SELECT v, 
                   ($earthRadius * ACOS(
                       COS(RADIANS(:lat)) * 
                       COS(RADIANS(vp.latitude)) * 
                       COS(RADIANS(vp.longitude) - RADIANS(:lon)) + 
                       SIN(RADIANS(:lat)) * 
                       SIN(RADIANS(vp.latitude))
                   )) AS distance
            FROM App\Entity\Volunteer v
            JOIN v.volunteerProfile vp
            WHERE v.id != :excludeId
            AND vp.latitude IS NOT NULL
            AND vp.longitude IS NOT NULL
            HAVING distance <= :maxDistance
            ORDER BY distance ASC
        DQL;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'lat' => $latitude,
            'lon' => $longitude,
            'maxDistance' => $maxDistanceKm,
            'excludeId' => $excludeVolunteerId,
        ]);

        return array_map(
            fn ($result) => $result[0],
            $query->getResult()
        );
    }

    //    /**
    //     * @return Volunteer[] Returns an array of Volunteer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Volunteer
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
