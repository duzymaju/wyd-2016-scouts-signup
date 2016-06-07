<?php

namespace Wyd2016Bundle\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

/**
 * Model
 */
class Volunteer extends ParticipantAbstract implements PersonInterface
{
    use PersonTrait;

    /** @var string|null */
    protected $associationName;

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

    /** @var string|null */
    protected $fatherName;

    /** @var integer */
    protected $serviceMainId;

    /** @var integer|null */
    protected $serviceExtraId;

    /** @var ArrayCollection */
    protected $permissions;

    /** @var string|null */
    protected $otherPermissions;

    /** @var ArrayCollection */
    protected $languages;

    /** @var string|null */
    protected $profession;

    /** @var string|null */
    protected $wydFormPassword;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

    /**
     * Get association name
     *
     * @return string|null
     */
    public function getAssociationName()
    {
        return $this->associationName;
    }

    /**
     * Set association name
     *
     * @param string|null $associationName association name
     *
     * @return self
     */
    public function setAssociationName($associationName = null)
    {
        $this->associationName = $associationName;

        return $this;
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
        try {
            $birthDate = new DateTime($year . '-' . $month . '-' . $day);
        } catch (Exception $e) {
            $birthDate = null;
        }

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
     * Get father name
     *
     * @return string|null
     */
    public function getFatherName()
    {
        return $this->fatherName;
    }

    /**
     * Set father name
     *
     * @param string|null $fatherName father name
     *
     * @return self
     */
    public function setFatherName($fatherName = null)
    {
        $this->fatherName = $fatherName;

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
     * @return ArrayCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add permission
     *
     * @param Permission $permission permission
     *
     * @return self
     */
    public function addPermission(Permission $permission)
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    /**
     * Remove permission
     *
     * @param Permission $permission permission
     *
     * @return self
     */
    public function removePermission(Permission $permission)
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
        }

        return $this;
    }

    /**
     * Set permissions
     *
     * @param ArrayCollection $permissions permissions
     *
     * @return self
     */
    public function setPermissions(ArrayCollection $permissions)
    {
        $this->permissions = $permissions;

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
     * Get WYD form password
     *
     * @return string|null
     */
    public function getWydFormPassword()
    {
        return $this->wydFormPassword;
    }

    /**
     * Set WYD form password
     *
     * @param string|null $wydFormPassword WYD form password
     *
     * @return self
     */
    public function setWydFormPassword($wydFormPassword = null)
    {
        $this->wydFormPassword = $wydFormPassword;

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
        if (!($this->permissions instanceof Collection)) {
            $this->permissions = new ArrayCollection();
        }
    }
}
