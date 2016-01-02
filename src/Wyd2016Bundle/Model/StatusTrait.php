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

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
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
    public function setStatus($status)
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
        return $this->status >= self::STATUS_CONFIRMED;
    }

    /**
     * Is payed
     *
     * @return boolean
     */
    public function isPayed()
    {
        return $this->status >= self::STATUS_PAYED;
    }

    /**
     * Get activation hash
     *
     * @return string
     */
    public function getActivationHash()
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
    public function setActivationHash($activationHash)
    {
        $this->activationHash = $activationHash;

        return $this;
    }
}
