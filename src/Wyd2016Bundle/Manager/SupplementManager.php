<?php

namespace Wyd2016Bundle\Manager;

use DateTime;
use Wyd2016Bundle\Model\Virtual\VolunteerSupplement;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Model\Volunteer;

/**
 * Manager
 */
class SupplementManager
{
    /** @var RegistrationLists */
    protected $registrationLists;

    /**
     * Set registration lists
     *
     * @param RegistrationLists $registrationLists registration lists
     *
     * @return self
     */
    public function setRegistrationLists(RegistrationLists $registrationLists)
    {
        $this->registrationLists = $registrationLists;

        return $this;
    }

    /**
     * Get volunteer supplement
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return VolunteerSupplement
     */
    public function getVolunteerSupplement(Volunteer $volunteer)
    {
        $volunteerSupplement = new VolunteerSupplement();
        $volunteerSupplement->setAskForDistrict($this->ifAskForDistrict($volunteer))
            ->setAskForFatherName($this->ifAskForFatherName($volunteer))
            ->setAskForService($this->ifAskForService($volunteer))
            ->setAskForShirtSize($this->ifAskForShirtSize($volunteer))
            ->setAskForDates($this->ifAskForDates($volunteer));

        return $volunteerSupplement;
    }

    /**
     * If ask for district
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForDistrict(Volunteer $volunteer)
    {
        $districtId = $volunteer->getDistrictId();
        if (empty($districtId)) {
            return false;
        }

        if ($volunteer->getUpdatedAt() >= new DateTime('2016-06-03 02:00:00')) {
            return false;
        }

        $districts = $this->registrationLists->getDistricts($volunteer->getRegionId());
        $districtIds = array_keys($districts);
        if (array_shift($districtIds) != $districtId) {
            return false;
        }

        if ($volunteer->isTroopMember() && !$volunteer->isTroopLeader()) {
            return false;
        }

        return true;
    }

    /**
     * If ask for father name
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForFatherName(Volunteer $volunteer)
    {
        $fatherName = $volunteer->getFatherName();
        if (!empty($fatherName)) {
            return false;
        }

        return true;
    }

    /**
     * If ask for service
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForService(Volunteer $volunteer)
    {
        if ($volunteer->getServiceMainId() != RegistrationLists::SERVICE_UNDERAGE) {
            return false;
        }

        if ($volunteer->isTroopMember() && !$volunteer->isTroopLeader()) {
            return false;
        }

        return true;
    }

    /**
     * If ask for shirt size
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForShirtSize(Volunteer $volunteer)
    {
        $shirtSize = $volunteer->getShirtSize();
        if (!empty($shirtSize)) {
            return false;
        }

        return true;
    }

    /**
     * If ask for dates
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForDates(Volunteer $volunteer)
    {
        $dates = array_keys($this->registrationLists->getVolunteerDates());
        if (in_array($volunteer->getDatesId(), $dates)) {
            return false;
        }

        if ($volunteer->isTroopMember() && !$volunteer->isTroopLeader()) {
            return false;
        }

        return true;
    }
}
