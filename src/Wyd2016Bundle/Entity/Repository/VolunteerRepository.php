<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Wyd2016Bundle\Form\RegistrationLists;

/**
 * Repository
 */
class VolunteerRepository extends EntityRepository implements BaseRepositoryInterface
{
    use BaseRepositoryTrait;

    /** @var RegistrationLists */
    protected $registrationLists;

    /**
     * Set registration lists
     *
     * @param RegistrationLists $registrationLists registration lists
     * 
     * @return self
     */
    public function setRegistrationLists(RegistrationLists $registrationLists)
    {
        $this->registrationLists = $registrationLists;

        return $this;
    }

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

    /**
     * Count by countries
     *
     * @return array
     */
    public function countByCountries()
    {
        $results = $this->getEntityManager()
            ->createQuery('SELECT COUNT(v.id) AS counter, v.country FROM Wyd2016Bundle:Volunteer v GROUP BY v.country')
            ->getResult();

        $countries = array();
        foreach ($results as $result) {
            if (!empty($result['country'])) {
                $countries[$result['country']] = (integer) $result['counter'];
            }
        }
        arsort($countries);

        return $countries;
    }

    /**
     * Count by regions
     *
     * @return array
     */
    public function countByRegions()
    {
        $results = $this->getEntityManager()
            ->createQuery('SELECT COUNT(v.id) AS counter, v.regionId FROM Wyd2016Bundle:Volunteer v GROUP BY v.regionId')
            ->getResult();

        $regions = array();
        $structure = $this->registrationLists->getRegions();
        foreach (array_keys($structure) as $regionId) {
            $regions[$regionId] = 0;
        }
        foreach ($results as $result) {
            if (array_key_exists($result['regionId'], $regions)) {
                $regions[$result['regionId']] = (integer) $result['counter'];
            }
        }

        return $regions;
    }

    /**
     * Count by services
     *
     * @return array
     */
    public function countByServices()
    {
        $resultsMain = $this->getEntityManager()
            ->createQuery('SELECT COUNT(v.id) AS counter, v.serviceMainId FROM Wyd2016Bundle:Volunteer v GROUP BY v.serviceMainId')
            ->getResult();

        $services = array();
        foreach ($resultsMain as $result) {
            if (!empty($result['serviceMainId'])) {
                $services[$result['serviceMainId']] = array(
                    'extra' => 0,
                    'main' => (integer) $result['counter'],
                );
            }
        }
        uasort($services, function ($a, $b) {
            return $a['main'] == $b['main'] ? 0 : ($a['main'] < $b['main'] ? 1 : -1);
        });

        $resultsExtra = $this->getEntityManager()
            ->createQuery('SELECT COUNT(v.id) AS counter, v.serviceExtraId FROM Wyd2016Bundle:Volunteer v GROUP BY v.serviceExtraId')
            ->getResult();

        foreach ($resultsExtra as $result) {
            if (!empty($result['serviceExtraId'])) {
                if (array_key_exists($result['serviceExtraId'], $services)) {
                    $services[$result['serviceExtraId']]['extra'] = (integer) $result['counter'];
                } else {
                    $services[$result['serviceExtraId']] = array(
                        'extra' => (integer) $result['counter'],
                        'main' => 0,
                    );
                }
            }
        }

        return $services;
    }
}
