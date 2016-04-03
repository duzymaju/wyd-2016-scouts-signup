<?php

namespace Wyd2016Bundle\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Model
 */
class Volunteer extends ParticipantAbstract implements PersonInterface
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

    /** @var string|null */
    protected $otherPermissions;

    /** @var ArrayCollection */
    protected $languages;

    /** @var string|null */
    protected $profession;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

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

        if (!empty($pesel)) {
            $this->setBirthDate($this->getBirthDateFromPesel());
            $this->setSex($this->getSexFromPesel());
        }

        return $this;
    }

    /**
     * Get birth date from PESEL
     * 
     * @return DateTime|null
     */
    public function getBirthDateFromPesel()
    {
        if (empty($this->pesel)) {
            return null;
        }

        $year = (integer) substr($this->pesel, 0, 2);
        $month = (integer) substr($this->pesel, 2, 2);
        $day = (integer) substr($this->pesel, 4, 2);

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

        return $birthDate;
    }

    /**
     * Get sex from PESEL
     *
     * @return string|null
     */
    public function getSexFromPesel()
    {
        if (empty($this->pesel)) {
            return null;
        }

        $sex = preg_match('#^[02468]$#', substr($this->pesel, 9, 1)) ? self::SEX_FEMALE : self::SEX_MALE;

        return $sex;
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
     * Get other permissions
     *
     * @return string|null
     */
    public function getOtherPermissions()
    {
        return $this->otherPermissions;
    }

    /**
     * Set other permissions
     *
     * @param string|null $otherPermissions other permissions
     *
     * @return self
     */
    public function setOtherPermissions($otherPermissions = null)
    {
        $this->otherPermissions = $otherPermissions;

        return $this;
    }

    /**
     * Get languages
     *
     * @return ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Add language
     *
     * @param Language $language language
     *
     * @return self
     */
    public function addLanguage(Language $language)
    {
        if (!$this->languages->contains($language)) {
            $this->languages->add($language);
        }

        return $this;
    }

    /**
     * Remove language
     *
     * @param Language $language language
     *
     * @return self
     */
    public function removeLanguage(Language $language)
    {
        if ($this->languages->contains($language)) {
            $this->languages->removeElement($language);
        }

        return $this;
    }

    /**
     * Set languages
     *
     * @param ArrayCollection $languages languages
     *
     * @return self
     */
    public function setLanguages(ArrayCollection $languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string|null
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set profession
     *
     * @param string|null $profession profession
     *
     * @return self
     */
    public function setProfession($profession = null)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Initialize collections
     */
    public function initializeCollections()
    {
        if (!($this->languages instanceof Collection)) {
            $this->languages = new ArrayCollection();
        }
    }
}
