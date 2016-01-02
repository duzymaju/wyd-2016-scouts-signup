<?php

namespace Wyd2016Bundle\Model;

use DateTime;

/**
 * Model
 */
class Pilgrim implements StatusInterface
{
    use IdTrait;
    use StatusTrait;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var DateTime */
    protected $birthDate;

    /** @var string */
    protected $country;

    /** @var string */
    protected $address;

    /** @var string */
    protected $phone;

    /** @var string */
    protected $email;

    /** @var DateTime */
    protected $dateFrom;

    /** @var DateTime */
    protected $dateTo;

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
     * Get birth date
     *
     * @return DateTime
     */
    function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birth date
     *
     * @param DateTime $birthDate birth date
     *
     * @return self
     */
    function setBirthDate(DateTime $birthDate)
    {
        $this->birthDate = $birthDate;

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
     * Get e-mail
     *
     * @return string
     */
    function getEmail()
    {
        return $this->email;
    }

    /**
     * Set e-mail
     *
     * @param string $email e-mail
     *
     * @return self
     */
    function setEmail($email)
    {
        $this->email = $email;

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
}
