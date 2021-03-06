<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class PilgrimRepository extends EntityRepository implements BaseRepositoryInterface, SearchRepositoryInterface
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

    /**
     * {@inheritdoc}
     */
    public function searchBy(array $queries)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('p');

        $i = 1;
        foreach ($queries as $query) {
            if (!is_numeric($query)) {
                $qb->orWhere('p.firstName LIKE :firstName_' .$i)
                    ->setParameter('firstName_' .$i, '%' . $query . '%');

                $qb->orWhere('p.lastName LIKE :lastName_' .$i)
                    ->setParameter('lastName_' .$i, '%' . $query . '%');
            }

            $qb->orWhere('p.address LIKE :address_' .$i)
                ->setParameter('address_' .$i, '%' . $query . '%');

            $qb->orWhere('p.phone LIKE :phone_' .$i)
                ->setParameter('phone_' .$i, '%' . $query . '%');

            $qb->orWhere('p.email LIKE :email_' .$i)
                ->setParameter('email_' .$i, $query);

            $i++;
        }
        $results = $qb->getQuery()
            ->getResult();

        return $results;
    }
}
