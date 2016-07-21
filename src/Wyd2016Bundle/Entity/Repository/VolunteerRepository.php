<?php

namespace Wyd2016Bundle\Entity\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Wyd2016Bundle\Form\RegistrationLists;

/**
 * Repository
 */
class VolunteerRepository extends EntityRepository implements BaseRepositoryInterface, SearchRepositoryInterface
{
    use BaseRepositoryTrait;

    /** @var RegistrationLists */
    protected $registrationLists;

    /** @var integer */
    protected $totalNumber;

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
     * Get full info by
     *
     * @param array $criteria criteria
     * @param array $orderBy  order by
     *
     * @return array
     */
    public function getFullInfoBy(array $criteria = array(), array $orderBy = array())
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('v')
            ->select('v, t')
            ->leftJoin('v.troop', 't')
            ->leftJoin('v.languages', 'l')
            ->leftJoin('v.permissions', 'p');
        foreach ($criteria as $column => $value) {
            $columnParts = explode('.', $column);
            $sign = count($columnParts) == 2 ? array_pop($columnParts) : null;
            $column = array_shift($columnParts);
            if (is_array($value)) {
                $condition = 'v.' . $column . ($sign == 'not' ? ' NOT' : '') . ' IN (:' . $column . ')';
            } elseif ($sign == 'lt') {
                $condition = 'v.' . $column . ' < :' . $column;
            } elseif ($sign == 'gt') {
                $condition = 'v.' . $column . ' > :' . $column;
            } elseif ($sign == 'lte') {
                $condition = 'v.' . $column . ' <= :' . $column;
            } elseif ($sign == 'gte') {
                $condition = 'v.' . $column . ' >= :' . $column;
            } else {
                $condition = 'v.' . $column . ' = :' . $column;
            }
            $qb->andWhere($condition)
                ->setParameter($column, $value);
        }
        foreach ($orderBy as $column => $direction) {
            $qb->addOrderBy('v.' . $column, $direction);
        }
        $results = $qb->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Get all ordered by
     *
     * @param array $orderBy order by
     *
     * @return array
     */
    public function getAllOrderedBy(array $orderBy = array())
    {
        $results = $this->getFullInfoBy(array(), $orderBy);

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

    /**
     * Get total number
     *
     * @param boolean $force force
     *
     * @return integer
     */
    public function getTotalNumber($force = false)
    {
        if (!isset($this->totalNumber) || $force) {
            $qb = $this->getEntityManager()
                ->createQueryBuilder();
            $qb->select('count(v.id)');
            $qb->from('Wyd2016Bundle:Volunteer', 'v');

            $this->totalNumber = $qb->getQuery()
                ->getSingleScalarResult();
        }

        return $this->totalNumber;
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

    /**
     * {@inheritdoc}
     */
    public function searchBy(array $queries)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('v');

        $i = 1;
        foreach ($queries as $query) {
            if (!is_numeric($query)) {
                $qb->orWhere('v.firstName LIKE :firstName_' .$i)
                    ->setParameter('firstName_' .$i, '%' . $query . '%');

                $qb->orWhere('v.lastName LIKE :lastName_' .$i)
                    ->setParameter('lastName_' .$i, '%' . $query . '%');
            } else {
                $queryInteger = (integer) $query;
                if ($queryInteger > 0) {
                    $qb->orWhere('v.pesel LIKE :pesel_' .$i)
                        ->setParameter('pesel_' .$i, (integer) $query);
                }
            }

            $qb->orWhere('v.address LIKE :address_' .$i)
                ->setParameter('address_' .$i, '%' . $query . '%');

            $qb->orWhere('v.phone LIKE :phone_' .$i)
                ->setParameter('phone_' .$i, '%' . $query . '%');

            $qb->orWhere('v.email LIKE :email_' .$i)
                ->setParameter('email_' .$i, $query);

            $i++;
        }
        $results = $qb->getQuery()
            ->getResult();

        return $results;
    }
}
