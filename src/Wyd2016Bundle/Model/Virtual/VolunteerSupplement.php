<?php

namespace Wyd2016Bundle\Model\Virtual;

/**
 * Model virtual
 */
class VolunteerSupplement
{
    /** @var boolean */
    protected $askForDistrict;

    /** @var boolean */
    protected $askForFatherName;

    /** @var boolean */
    protected $askForService;

    /** @var boolean */
    protected $askForShirtSize;

    /** @var boolean */
    protected $askForDates;

    /**
     * If ask for district
     *
     * @return boolean
     */
    public function ifAskForDistrict()
    {
        return $this->askForDistrict;
    }

    /**
     * Set ask for district
     *
     * @param boolean $askForDistrict ask for district
     *
     * @return self
     */
    public function setAskForDistrict($askForDistrict)
    {
        $this->askForDistrict = $askForDistrict;

        return $this;
    }

    /**
     * If ask for father name
     *
     * @return boolean
     */
    public function ifAskForFatherName()
    {
        return $this->askForFatherName;
    }

    /**
     * Set ask for father name
     *
     * @param boolean $askForFatherName ask for father name
     *
     * @return self
     */
    public function setAskForFatherName($askForFatherName)
    {
        $this->askForFatherName = $askForFatherName;

        return $this;
    }

    /**
     * If ask for service
     *
     * @return boolean
     */
    public function ifAskForService()
    {
        return $this->askForService;
    }

    /**
     * Set ask for service
     *
     * @param boolean $askForService ask for service
     *
     * @return self
     */
    public function setAskForService($askForService)
    {
        $this->askForService = $askForService;

        return $this;
    }

    /**
     * If ask for shirt size
     *
     * @return boolean
     */
    public function ifAskForShirtSize()
    {
        return $this->askForShirtSize;
    }

    /**
     * Set ask for shirt size
     *
     * @param boolean $askForShirtSize ask for shirt size
     *
     * @return self
     */
    public function setAskForShirtSize($askForShirtSize)
    {
        $this->askForShirtSize = $askForShirtSize;

        return $this;
    }

    /**
     * If ask for dates
     *
     * @return boolean
     */
    public function ifAskForDates()
    {
        return $this->askForDates;
    }

    /**
     * Set ask for dates
     *
     * @param boolean $askForDates ask for dates
     *
     * @return self
     */
    public function setAskForDates($askForDates)
    {
        $this->askForDates = $askForDates;

        return $this;
    }

    /**
     * If ask for anything
     *
     * @return boolean
     */
    public function ifAskForAnything()
    {
        $askForAnything = $this->ifAskForDistrict() || $this->ifAskForFatherName() || $this->ifAskForService() ||
            $this->ifAskForShirtSize() || $this->ifAskForDates();

        return $askForAnything;
    }
}
