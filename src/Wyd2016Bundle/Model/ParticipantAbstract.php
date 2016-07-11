<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
abstract class ParticipantAbstract
{
    use RecordTrait;

    /** @const integer */
    const STATUS_CONFIRMED = 1;

    /** @const integer */
    const STATUS_PAYED = 2;

    /** @const integer */
    const STATUS_RESIGNED = 6;

    /** @const integer */
    const STATUS_NOT_CONFIRMED = 0;
    
    /** @var integer */
    protected $status;

    /** @var string */
    protected $activationHash;

    /** @var integer */
    protected $datesId;

    /** @var string */
    protected $comments;

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
     * Is resigned
     *
     * @return boolean
     */
    public function isResigned()
    {
        return $this->status >= self::STATUS_RESIGNED;
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

    /**
     * Get dates ID
     *
     * @return integer
     */
    public function getDatesId()
    {
        return $this->datesId;
    }

    /**
     * Set dates ID
     *
     * @param integer $datesId dates ID
     *
     * @return self
     */
    public function setDatesId($datesId)
    {
        $this->datesId = $datesId;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string|null
     */
    public function getComments()
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
    public function setComments($comments = null)
    {
        $this->comments = $comments;

        return $this;
    }
}
