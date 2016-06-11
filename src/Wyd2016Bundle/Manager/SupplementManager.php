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
            ->setAskForShirtSize($this->ifAskForShirtSize($volunteer));

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

        if (!$this->isTroopLeader($volunteer)) {
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

        if (!$this->isTroopLeader($volunteer)) {
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
     * Is troop leader
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function isTroopLeader(Volunteer $volunteer)
    {
        $troop = $volunteer->getTroop();
        $isTroopLeader = isset($troop) && $troop->getLeader() == $volunteer;

        return $isTroopLeader;
    }
}
