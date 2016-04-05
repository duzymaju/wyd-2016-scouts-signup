<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
class Permission
{
    /** @var Volunteer */
    protected $volunteer;

    /** @var integer */
    protected $id;

    /**
     * Get volunteer
     *
     * @return Volunteer
     */
    public function getVolunteer()
    {
        return $this->volunteer;
    }

    /**
     * Set volunteer
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return self
     */
    public function setVolunteer(Volunteer $volunteer)
    {
        $this->volunteer = $volunteer;

        return $this;
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param integer $id ID
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
