<?php

namespace Wyd2016Bundle\Model;

use DateTime;

/**
 * Model
 */
class Volunteer extends ParticipantAbstract
{
    use PersonTrait;

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

    /**
     * Get grade ID
     *
     * @return integer|null
     */
    public function getGradeId()
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
    public function setGradeId($gradeId = null)
    {
        $this->gradeId = $gradeId;

        return $this;
    }

    /**
     * Get region ID
     *
     * @return integer|null
     */
    public function getRegionId()
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
    public function setRegionId($regionId = null)
    {
        $this->regionId = $regionId;

        return $this;
    }

    /**
     * Get district ID
     *
     * @return integer|null
     */
    public function getDistrictId()
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
    public function setDistrictId($districtId = null)
    {
        $this->districtId = $districtId;

        return $this;
    }

    /**
     * Get troop
     *
     * @return Troop|null
     */
    public function getTroop()
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
    public function setTroop(Troop $troop = null)
    {
        $this->troop = $troop;

        return $this;
    }

    /**
     * Get PESEL
     *
     * @return string|null
     */
    public function getPesel()
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
    public function setPesel($pesel = null)
    {
        $this->pesel = $pesel;

        $year = (integer) substr($pesel, 0, 2);
        $month = (integer) substr($pesel, 2, 2);
        $day = (integer) substr($pesel, 4, 2);

        if ($month > 20 && $month < 33) {
            $month -= 20;
            $year += 2000;
        } elseif ($month > 40 && $month < 53) {
            $month -= 40;
            $year += 2100;
        } elseif ($month > 60 && $month < 73) {
            $month -= 60;
            $year += 2200;
        } elseif ($month > 80 && $month < 93) {
            $month -= 80;
            $year += 1800;
        } else {
            $year += 1900;
        }

        $birthDate = new DateTime($year . '-' . $month . '-' . $day);
        $this->setBirthDate($birthDate);

        return $this;
    }

    /**
     * Get service main ID
     *
     * @return integer
     */
    public function getServiceMainId()
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
    public function setServiceMainId($serviceMainId)
    {
        $this->serviceMainId = $serviceMainId;

        return $this;
    }

    /**
     * Get service extra ID
     *
     * @return integer|null
     */
    public function getServiceExtraId()
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
    public function setServiceExtraId($serviceExtraId = null)
    {
        $this->serviceExtraId = $serviceExtraId;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return string
     */
    public function getPermissions()
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
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get languages
     *
     * @return string
     */
    public function getLanguages()
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
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
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
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }
}
