<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class PilgrimRepository extends EntityRepository implements BaseRepositoryInterface
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
        $qb = $this->createQueryBuilder('p')
            ->select('p, g')
            ->leftJoin('p.group', 'g');
        foreach ($orderBy as $column => $direction) {
            $qb->addOrderBy('p.' . $column, $direction);
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
        $qb->select('count(p.id)');
        $qb->from('Wyd2016Bundle:Pilgrim', 'p');

        $count = $qb->getQuery()
            ->getSingleScalarResult();

        return $count;
    }
}
