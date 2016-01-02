<?php

namespace Wyd2016Bundle\Model;

use DateTime;

/**
 * Model
 */
class Volunteer implements StatusInterface
{
    use IdTrait;
    use PersonTrait;
    use RecordTrait;
    use StatusTrait;

    /** @var integer */
    protected $gradeId;

    /** @var integer */
    protected $regionId;

    /** @var Troop */
    protected $troop;

    /** @var string */
    protected $pesel;

    /** @var integer */
    protected $serviceId;

    /** @var string */
    protected $permissions;

    /** @var string */
    protected $languages;

    /** @var string */
    protected $profession;

    /** @var DateTime */
    protected $dateFrom;

    /** @var DateTime */
    protected $dateTo;

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
     * Get troop
     *
     * @return Troop
     */
    function getTroop()
    {
        return $this->troop;
    }

    /**
     * Set troop
     *
     * @param Troop $troop troop
     *
     * @return self
     */
    function setTroop(Troop $troop)
    {
        $this->troop = $troop;

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
