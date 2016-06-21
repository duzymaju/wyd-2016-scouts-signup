<?php

namespace Wyd2016Bundle\Twig;

use DateTime;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFilter;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Form\RegistrationLists;

/**
 * Twig
 */
class WydExtension extends Twig_Extension
{
    /** @var RegistrationLists */
    protected $registrationLists;

    /** @var DateTime */
    protected $ageLimit;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator     translator
     * @param integer             $shortTermLimit short term limit
     * @param string              $ageLimit       age limit
     */
    public function __construct(TranslatorInterface $translator, $shortTermLimit, $ageLimit)
    {
        $this->registrationLists = new RegistrationLists($translator, $shortTermLimit);
        $this->ageLimit = (new DateTime($ageLimit))->modify('-1 day');
    }

    /**
     * Set volunteer repository
     *
     * @param VolunteerRepository $volunteerRepository volunteer repository
     *
     * @return self
     */
    public function setVolunteerRepository(VolunteerRepository $volunteerRepository)
    {
        $this->registrationLists->setVolunteerRepository($volunteerRepository);

        return $this;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array(
            new Twig_SimpleFilter('ageatlimit', array($this, 'ageAtLimitFilter')),
            new Twig_SimpleFilter('changekeys', array($this, 'changeKeysFilter')),
            new Twig_SimpleFilter('districtname', array($this, 'districtNameFilter')),
            new Twig_SimpleFilter('gradename', array($this, 'gradeNameFilter')),
            new Twig_SimpleFilter('languagename', array($this, 'languageNameFilter')),
            new Twig_SimpleFilter('localizedcountry', array($this, 'localizedCountryFilter')),
            new Twig_SimpleFilter('permissionname', array($this, 'permissionNameFilter')),
            new Twig_SimpleFilter('peselmodify', array($this, 'peselModifyFilter')),
            new Twig_SimpleFilter('pilgrimdate', array($this, 'pilgrimDateFilter')),
            new Twig_SimpleFilter('regionname', array($this, 'regionNameFilter')),
            new Twig_SimpleFilter('servicename', array($this, 'serviceNameFilter')),
            new Twig_SimpleFilter('sexname', array($this, 'sexNameFilter')),
            new Twig_SimpleFilter('shirtsizename', array($this, 'shirtSizeNameFilter')),
            new Twig_SimpleFilter('statusname', array($this, 'statusNameFilter')),
            new Twig_SimpleFilter('volunteerdate', array($this, 'volunteerDateFilter')),
        );

        return $filters;
    }

    /**
     * Age at limit filter
     *
     * @param DateTime $birthDate birth date
     *
     * @return integer
     */
    public function ageAtLimitFilter(DateTime $birthDate)
    {
        $ageAtLimit = (integer) $birthDate->diff($this->ageLimit)
            ->format('%y');

        return $ageAtLimit;
    }

    /**
     * Change keys filter
     *
     * @param array $array      array
     * @param array $keysMapper keys mapper
     *
     * @return array
     */
    public function changeKeysFilter(array $array, array $keysMapper)
    {
        foreach ($keysMapper as $oldKey => $newKey) {
            if (array_key_exists($oldKey, $array)) {
                $array[$newKey] = $array[$oldKey];
                unset($array[$oldKey]);
            }
        }

        return $array;
    }

    /**
     * District name filter
     *
     * @param integer $districtId district ID
     *
     * @return string|null
     */
    public function districtNameFilter($districtId)
    {
        $districtName = $this->registrationLists->getDistrict($districtId);

        return $districtName;
    }

    /**
     * Grade name filter
     *
     * @param integer $gradeId grade ID
     *
     * @return string|null
     */
    public function gradeNameFilter($gradeId)
    {
        $gradeName = $this->registrationLists->getGrade($gradeId);

        return $gradeName;
    }

    /**
     * Language name filter
     *
     * @param string $languageSlug language slug
     *
     * @return string|null
     */
    public function languageNameFilter($languageSlug)
    {
        $languageName = $this->registrationLists->getLanguage($languageSlug);

        return $languageName;
    }

    /**
     * Localized country filter
     *
     * @param string $countryCode country code
     * 
     * @return string
     */
    public function localizedCountryFilter($countryCode)
    {
        $regionBundle = Intl::getRegionBundle();
        $countryName = $regionBundle->getCountryName($countryCode);

        return $countryName;
    }

    /**
     * Permission name filter
     *
     * @param integer $permissionId permission ID
     *
     * @return string|null
     */
    public function permissionNameFilter($permissionId)
    {
        $permissionName = $this->registrationLists->getPermission($permissionId);

        return $permissionName;
    }

    /**
     * PESEL modify filter
     *
     * @param string  $pesel     PESEL
     * @param boolean $showWhole show whole
     *
     * @return string|null
     */
    public function peselModifyFilter($pesel, $showWhole = false)
    {
        if (empty($pesel)) {
            $modifiedPesel = null;
        } else {
            $formattedPesel = str_repeat('0', 11 - strlen($pesel)) . $pesel;
            $modifiedPesel = $showWhole ? $formattedPesel : substr($formattedPesel, 0, 6) . '*****';
        }

        return $modifiedPesel;
    }

    /**
     * Pilgrim date filter
     *
     * @param integer $pilgrimDateId pilgrim date ID
     *
     * @return string|null
     */
    public function pilgrimDateFilter($pilgrimDateId)
    {
        $pilgrimDate = $this->registrationLists->getPilgrimDate($pilgrimDateId);

        return $pilgrimDate;
    }

    /**
     * Region name filter
     *
     * @param integer $regionId region ID
     *
     * @return string|null
     */
    public function regionNameFilter($regionId)
    {
        $regionName = $this->registrationLists->getRegion($regionId);

        return $regionName;
    }

    /**
     * Service name filter
     *
     * @param integer $serviceId service ID
     *
     * @return string|null
     */
    public function serviceNameFilter($serviceId)
    {
        $serviceName = $this->registrationLists->getService($serviceId);

        return $serviceName;
    }

    /**
     * Sex name filter
     *
     * @param string $sexId sex ID
     *
     * @return string|null
     */
    public function sexNameFilter($sexId)
    {
        $sexName = $this->registrationLists->getSex($sexId);

        return $sexName;
    }

    /**
     * Shirt size name filter
     *
     * @param integer $shirtSizeId shirt size ID
     *
     * @return string|null
     */
    public function shirtSizeNameFilter($shirtSizeId)
    {
        $shirtSizeName = $this->registrationLists->getShirtSize($shirtSizeId);

        return $shirtSizeName;
    }

    /**
     * Status name filter
     *
     * @param integer $status status
     *
     * @return string|null
     */
    public function statusNameFilter($status)
    {
        $statusName = $this->registrationLists->getStatus($status);

        return $statusName;
    }

    /**
     * Volunteer date filter
     *
     * @param integer $volunteerDateId volunteer date ID
     *
     * @return string|null
     */
    public function volunteerDateFilter($volunteerDateId)
    {
        $volunteerDate = $this->registrationLists->getVolunteerDate($volunteerDateId);

        return $volunteerDate;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'wyd_extension';
    }
}
