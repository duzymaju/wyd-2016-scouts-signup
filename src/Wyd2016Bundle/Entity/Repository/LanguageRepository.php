<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class LanguageRepository extends EntityRepository implements BaseRepositoryInterface
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
            ->createQuery('SELECT COUNT(l.volunteer) AS counter, l.slug FROM Wyd2016Bundle:Language l GROUP BY l.slug')
            ->getResult();

        $languages = array();
        foreach ($results as $result) {
            $languages[$result['slug']] = (integer) $result['counter'];
        }

        return $languages;
    }
}
