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
