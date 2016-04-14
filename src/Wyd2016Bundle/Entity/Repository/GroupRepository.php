<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class GroupRepository extends EntityRepository implements BaseRepositoryInterface
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
        $qb->select('count(g.id)');
        $qb->from('Wyd2016Bundle:Group', 'g');

        $count = $qb->getQuery()
            ->getSingleScalarResult();

        return $count;
    }
}
