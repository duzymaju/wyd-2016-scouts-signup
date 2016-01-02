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

    /** @var integer|null */
    protected $gradeId;

    /** @var integer|null */
    protected $regionId;

    /** @var integer|null */
    protected $districtId;

    /** @var Troop|null */
    protected $troop;

    /** @var string|null */
    protected $pesel;

    /** @var integer */
    protected $serviceMainId;

    /** @var integer|null */
    protected $serviceExtraId;

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
     * @return integer|null
     */
    function getGradeId()
    {
        return $this->gradeId;
    }

    /**
     * Set grade ID
     *
     * @param integer|null $gradeId grade ID
     *
     * @return self
     */
    function setGradeId($gradeId = null)
    {
        $this->gradeId = $gradeId;

        return $this;
    }

    /**
     * Get region ID
     *
     * @return integer|null
     */
    function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * Set region ID
     *
     * @param integer|null $regionId region ID
     *
     * @return self
     */
    function setRegionId($regionId = null)
    {
        $this->regionId = $regionId;

        return $this;
    }

    /**
     * Get district ID
     *
     * @return integer|null
     */
    function getDistrictId()
    {
        return $this->districtId;
    }

    /**
     * Set district ID
     *
     * @param integer|null $districtId district ID
     *
     * @return self
     */
    function setDistrictId($districtId = null)
    {
        $this->districtId = $districtId;

        return $this;
    }

    /**
     * Get troop
     *
     * @return Troop|null
     */
    function getTroop()
    {
        return $this->troop;
    }

    /**
     * Set troop
     *
     * @param Troop|null $troop troop
     *
     * @return self
     */
    function setTroop(Troop $troop = null)
    {
        $this->troop = $troop;

        return $this;
    }

    /**
     * Get PESEL
     *
     * @return string|null
     */
    function getPesel()
    {
        return $this->pesel;
    }

    /**
     * Set PESEL
     *
     * @param string|null $pesel PESEL
     *
     * @return self
     */
    function setPesel($pesel = null)
    {
        $this->pesel = $pesel;

        return $this;
    }

    /**
     * Get service main ID
     *
     * @return integer
     */
    function getServiceMainId()
    {
        return $this->serviceMainId;
    }

    /**
     * Set service main ID
     *
     * @param integer $serviceMainId service main ID
     *
     * @return self
     */
    function setServiceMainId($serviceMainId)
    {
        $this->serviceMainId = $serviceMainId;

        return $this;
    }

    /**
     * Get service extra ID
     *
     * @return integer|null
     */
    function getServiceExtraId()
    {
        return $this->serviceExtraId;
    }

    /**
     * Set service extra ID
     *
     * @param integer|null $serviceExtraId service extra ID
     *
     * @return self
     */
    function setServiceExtraId($serviceExtraId = null)
    {
        $this->serviceExtraId = $serviceExtraId;

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
