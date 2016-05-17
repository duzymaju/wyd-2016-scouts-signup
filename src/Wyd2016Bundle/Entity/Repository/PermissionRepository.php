<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class PermissionRepository extends EntityRepository implements BaseRepositoryInterface
{
    use BaseRepositoryTrait;

    /**
     * Count by types
     *
     * @return array
     */
    public function countByTypes()
    {
        $results = $this->getEntityManager()
            ->createQuery('SELECT COUNT(p.volunteer) AS counter, p.id FROM Wyd2016Bundle:Permission p GROUP BY p.id')
            ->getResult();

        $permissions = array();
        foreach ($results as $result) {
            $permissions[$result['id']] = (integer) $result['counter'];
        }

        return $permissions;
    }
}
