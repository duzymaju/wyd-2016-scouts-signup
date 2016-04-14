<?php

namespace Wyd2016Bundle\Entity\Repository;

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
     * Get total number
     *
     * @return integer
     */
    public function getTotalNumber()
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder();
        $qb->select('count(v.id)');
        $qb->from('Wyd2016Bundle:Volunteer', 'v');

        $count = $qb->getQuery()
            ->getSingleScalarResult();

        return $count;
    }
}
