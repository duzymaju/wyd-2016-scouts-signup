<?php

namespace Wyd2016Bundle\Entity\Repository;

use Wyd2016Bundle\Entity\EntityInterface;

/**
 * Repository
 */
trait BaseRepositoryTrait
{
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
}
