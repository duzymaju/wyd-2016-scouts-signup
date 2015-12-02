<?php

namespace Wyd2016Bundle\Model;

use DateTime;

/**
 * Model
 */
class ScoutApplication
{
    use IdTrait;

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

    /** @var integer */
    protected $gradeId;

    /** @var integer */
    protected $regionId;

    /** @var string */
    protected $pesel;

    /** @var string */
    protected $address;

    /** @var integer */
    protected $serviceId;

    /** @var string */
    protected $permissions;

    /** @var string */
    protected $languages;

    /** @var string */
    protected $profession;
    
    /** @var string */
    protected $phone;

    /** @var string */
    protected $email;

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
     * Get grade ID
     *
     * @return integer
     */
    function getGradeId()
    {
        return $this->gradeId;
    }

    /**
     * Set grade ID
     *
     * @param integer $gradeId grade ID
     *
     * @return self
     */
    function setGradeId($gradeId)
    {
        $this->gradeId = $gradeId;

        return $this;
    }

    /**
     * Get region ID
     *
     * @return integer
     */
    function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * Set region ID
     *
     * @param integer $regionId region ID
     *
     * @return self
     */
    function setRegionId($regionId)
    {
        $this->regionId = $regionId;

        return $this;
    }

    /**
     * Get PESEL
     *
     * @return string
     */
    function getPesel()
    {
        return $this->pesel;
    }

    /**
     * Set PESEL
     *
     * @param string $pesel PESEL
     *
     * @return self
     */
    function setPesel($pesel)
    {
        $this->pesel = $pesel;

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
     * Get service ID
     *
     * @return integer
     */
    function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Set service ID
     *
     * @param integer $serviceId service ID
     *
     * @return self
     */
    function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return string
     */
    function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set permissions
     *
     * @param string $permissions permissions
     *
     * @return self
     */
    function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get languages
     *
     * @return string
     */
    function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set languages
     *
     * @param string $languages languages
     *
     * @return self
     */
    function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set profession
     *
     * @param string $profession profession
     *
     * @return self
     */
    function setProfession($profession)
    {
        $this->profession = $profession;

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
