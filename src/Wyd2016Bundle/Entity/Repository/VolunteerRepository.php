<?php

namespace Wyd2016Bundle\Entity\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class VolunteerRepository extends EntityRepository implements BaseRepositoryInterface
{
    use BaseRepositoryTrait;

    /**
     * Get all ordered by
     *
     * @param array|null $orderBy order by
     *
     * @return array
     */
    public function getAllOrderedBy(array $orderBy = null)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('v')
            ->select('v, t')
            ->leftJoin('v.troop', 't')
            ->leftJoin('v.languages', 'l')
            ->leftJoin('v.permissions', 'p');
        foreach ($orderBy as $column => $direction) {
            $qb->addOrderBy('v.' . $column, $direction);
        }
        $results = $qb->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get by region and time
     * 
     * @param integer  $regionId region ID
     * @param DateTime $timeFrom time from
     * @param DateTime $timeTo   time to
     *
     * @return array
     */
    public function getByRegionAndTime($regionId, DateTime $timeFrom, DateTime $timeTo)
    {
        $qb = $this->createQueryBuilder('v')
            ->select('v, t')
            ->leftJoin('v.troop', 't')
            ->where('v.regionId = :regionId')
            ->andWhere('v.createdAt BETWEEN :timeFrom AND :timeTo')
            ->orderBy('v.troop', 'ASC')
            ->setParameter('regionId', $regionId)
            ->setParameter('timeFrom', $timeFrom->format('Y-m-d'))
            ->setParameter('timeTo', $timeTo->format('Y-m-d'));
        $results = $qb->getQuery()
            ->getResult();

        return $results;
    }
}
