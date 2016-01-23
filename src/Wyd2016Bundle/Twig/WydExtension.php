<?php

namespace Wyd2016Bundle\Twig;

use DateTime;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFilter;
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
     * @param TranslatorInterface $translator translator
     * @param string              $ageLimit   age limit
     */
    public function __construct(TranslatorInterface $translator, $ageLimit)
    {
        $this->registrationLists = new RegistrationLists($translator);
        $this->ageLimit = (new DateTime($ageLimit))->modify('-1 day');
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
            new Twig_SimpleFilter('districtname', array($this, 'districtNameFilter')),
            new Twig_SimpleFilter('gradename', array($this, 'gradeNameFilter')),
            new Twig_SimpleFilter('languagename', array($this, 'languageNameFilter')),
            new Twig_SimpleFilter('localizedcountry', array($this, 'localizedCountryFilter')),
            new Twig_SimpleFilter('peselmodify', array($this, 'peselModifyFilter')),
            new Twig_SimpleFilter('pilgrimdate', array($this, 'pilgrimDateFilter')),
            new Twig_SimpleFilter('regionname', array($this, 'regionNameFilter')),
            new Twig_SimpleFilter('servicename', array($this, 'serviceNameFilter')),
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
     * @param string $languageId language ID
     *
     * @return string|null
     */
    public function languageNameFilter($languageId)
    {
        $languageName = $this->registrationLists->getLanguage($languageId);

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
     * PESEL modify filter
     *
     * @param string $pesel PESEL
     *
     * @return string
     */
    public function peselModifyFilter($pesel)
    {
        $modifiedPesel = substr($pesel, 0, 6) . '*****';

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
