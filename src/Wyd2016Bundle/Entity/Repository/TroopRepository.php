<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class TroopRepository extends EntityRepository implements BaseRepositoryInterface
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
        $qb = $this->createQueryBuilder('t')
            ->select('t, v')
            ->leftJoin('t.members', 'v');
        foreach ($orderBy as $column => $direction) {
            $qb->addOrderBy('t.' . $column, $direction);
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
        $qb->select('count(t.id)');
        $qb->from('Wyd2016Bundle:Troop', 't');

        $count = $qb->getQuery()
            ->getSingleScalarResult();

        return $count;
    }
}
