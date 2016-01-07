<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
class Language
{
    /** @var Volunteer */
    protected $volunteer;

    /** @var string */
    protected $slug;

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
     * Get volunteer
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
}
