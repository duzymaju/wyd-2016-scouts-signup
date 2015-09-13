<?php

namespace Wyd2016Bundle\Model;

use DateTime;

/**
 * Model
 */
class PilgrimApplication
{
    use IdTrait;

    /** @const integer */
    const ACCOMODATION_SCHOOL = 1;

    /** @const integer */
    const ACCOMODATION_TENT = 2;

    /** @const integer */
    const STATUS_CONFIRMED = 1;

    /** @const integer */
    const STATUS_NOT_CONFIRMED = 0;

    /** @var integer */
    protected $status;

    /** @var string */
    protected $activationHash;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $country;

    /** @var string */
    protected $address;

    /** @var string */
    protected $phone;

    /** @var string */
    protected $mail;

    /** @var integer */
    protected $accomodationId;

    /** @var DateTime */
    protected $dateFrom;

    /** @var DateTime */
    protected $dateTo;

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
     * Get first name
     *
     * @return string
     */
    function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set first name
     *
     * @param string $firstName first name
     *
     * @return self
     */
    function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get last name
     *
     * @return string
     */
    function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set last name
     *
     * @param string $lastName last name
     *
     * @return self
     */
    function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param integer $country country
     *
     * @return self
     */
    function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address address
     *
     * @return self
     */
    function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param string $phone phone
     *
     * @return self
     */
    function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    function getMail()
    {
        return $this->mail;
    }

    /**
     * Set mail
     *
     * @param string $mail mail
     *
     * @return self
     */
    function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get accomodation ID
     *
     * @return integer
     */
    function getAccomodationId()
    {
        return $this->accomodationId;
    }

    /**
     * Set accomodation ID
     *
     * @param integer $accomodationId accomodation ID
     *
     * @return self
     */
    function setAccomodationId($accomodationId)
    {
        $this->accomodationId = $accomodationId;

        return $this;
    }

    /**
     * Get date from
     *
     * @return DateTime
     */
    function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set date from
     *
     * @param DateTime $dateFrom date from
     *
     * @return self
     */
    function setDateFrom(DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get date to
     *
     * @return DateTime
     */
    function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set date to
     * 
     * @param DateTime $dateTo date to
     *
     * @return self
     */
    function setDateTo(DateTime $dateTo)
    {
        $this->dateTo = $dateTo;

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
