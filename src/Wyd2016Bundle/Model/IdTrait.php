<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
trait IdTrait
{
    /** @var integer */
    protected $id;

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param integer $id ID
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
