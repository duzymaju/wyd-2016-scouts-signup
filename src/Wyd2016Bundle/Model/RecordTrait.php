<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
trait RecordTrait
{
    /** @var string */
    protected $comments;

    /** @var DateTime */
    protected $createdAt;

    /**
     * Get comments
     *
     * @return string|null
     */
    function getComments()
    {
        return $this->comments;
    }

    /**
     * Set comments
     *
     * @param string|null $comments comments
     *
     * @return self
     */
    function setComments($comments = null)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get created at
     *
     * @return DateTime
     */
    function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set created at
     *
     * @param DateTime $createdAt created at
     *
     * @return self
     */
    function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
