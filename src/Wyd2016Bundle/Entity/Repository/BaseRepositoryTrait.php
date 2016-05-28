<?php

namespace Wyd2016Bundle\Entity\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Wyd2016Bundle\Entity\EntityInterface;
use Wyd2016Bundle\Model\Virtual\Paginator;

/**
 * Repository
 */
trait BaseRepositoryTrait
{
    /**
     * Find one by or throw NotFoundHttpException
     *
     * @param array      $criteria criteria
     * @param array|null $orderBy  order by
     *
     * @return object
     *
     * @throws NotFoundHttpException
     */
    public function findOneByOrException(array $criteria, array $orderBy = null)
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if (!isset($result)) {
            throw new NotFoundHttpException();
        }

        return $result;
    }

    /**
     * Insert
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function insert(EntityInterface $entity, $flush = false)
    {
        return $this->save($entity, $flush);
    }

    /**
     * Update
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function update(EntityInterface $entity, $flush = false)
    {
        return $this->save($entity, $flush);
    }

    /**
     * Delete
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    public function delete(EntityInterface $entity, $flush = false)
    {
        $this->getEntityManager()
            ->remove($entity);
        if ($flush) {
            $this->flush();
        }
        
        return $this;
    }

    /**
     * Flush
     *
     * @return self
     */
    public function flush()
    {
        $this->getEntityManager()
            ->flush();

        return $this;
    }
    
    /**
     * Save
     *
     * @param EntityInterface $entity entity
     * @param bool            $flush  flag, if flush should be done?
     *
     * @return self
     */
    protected function save(EntityInterface $entity, $flush = false)
    {
        $this->getEntityManager()
            ->persist($entity);
        if ($flush) {
            $this->flush();
        }
        return $this;
    }

    /**
     * Get pack
     *
     * @param integer $pageNo   page no
     * @param integer $packSize pack size
     * @param array   $criteria criteria
     * @param array   $orderBy  order by
     *
     * @return Paginator
     */
    public function getPack($pageNo, $packSize, array $criteria = array(), array $orderBy = array())
    {
        $alias = 't';
        $qb = $this->createQueryBuilder($alias);
        foreach ($criteria as $column => $value) {
            $qb->andWhere($alias . '.' . $column . ' = :' . $column)
                ->setParameter($column, $value);
        }
        foreach ($orderBy as $column => $direction) {
            $qb->addOrderBy($alias . '.' . $column, $direction);
        }
        $query = $qb->getQuery()
            ->setFirstResult($packSize * ($pageNo - 1))
            ->setMaxResults($packSize);

        $paginator = new Paginator($query, $pageNo, $packSize);

        return $paginator;
    }

    /**
     * Get pack or throw NotFoundHttpException
     *
     * @param integer $pageNo   page no
     * @param integer $packSize pack size
     * @param array   $criteria criteria
     * @param array   $orderBy  order by
     *
     * @return Paginator
     *
     * @throws NotFoundHttpException
     */
    public function getPackOrException($pageNo, $packSize, array $criteria = array(), array $orderBy = array())
    {
        $result = $this->getPack($pageNo, $packSize, $criteria, $orderBy);
        if ($pageNo > 1 && $result->getIterator()->count() == 0) {
            throw new NotFoundHttpException();
        }

        return $result;
    }
}
