<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
trait StatusTrait
{
    /** @var integer */
    protected $status;

    /** @var string */
    protected $activationHash;

    /** @var DateTime */
    protected $createdAt;

    /**
     * Get status
     *
     * @return string
     */
    function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status status
     *
     * @return self
     */
    function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Is confirmed
     *
     * @return boolean
     */
    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Get activation hash
     *
     * @return string
     */
    function getActivationHash()
    {
        return $this->activationHash;
    }

    /**
     * Set activation hash
     *
     * @param string $activationHash activation hash
     *
     * @return self
     */
    function setActivationHash($activationHash)
    {
        $this->activationHash = $activationHash;

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
