<?php

namespace Wyd2016Bundle\Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Model
 */
class Troop implements StatusInterface
{
    use IdTrait;
    use RecordTrait;
    use StatusTrait;

    /** @var string */
    protected $name;

    /** @var Volunteer */
    protected $leader;

    /** @var ArrayCollection */
    protected $members;

    /** @var DateTime */
    protected $dateFrom;

    /** @var DateTime */
    protected $dateTo;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

    /**
     * Get name
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name name
     *
     * @return self
     */
    function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get leader
     *
     * @return Volunteer
     */
    function getLeader()
    {
        return $this->leader;
    }

    /**
     * Set leader
     *
     * @param Volunteer $leader leader
     *
     * @return self
     */
    function setLeader(Volunteer $leader)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get members
     *
     * @return ArrayCollection
     */
    function getMembers()
    {
        return $this->members;
    }

    /**
     * Add member
     *
     * @param Volunteer $member member
     *
     * @return self
     */
    function addMember(Volunteer $member)
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    /**
     * Remove member
     *
     * @param Volunteer $member member
     *
     * @return self
     */
    function removeMember(Volunteer $member)
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

        return $this;
    }

    /**
     * Set members
     *
     * @param ArrayCollection $members members
     *
     * @return self
     */
    function setMembers(ArrayCollection $members)
    {
        $this->members = $members;

        return $this;
    }

    /**
     * Get date from
     *
     * @return DateTime
     */
    function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set date from
     *
     * @param DateTime $dateFrom date from
     *
     * @return self
     */
    function setDateFrom(DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get date to
     *
     * @return DateTime
     */
    function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set date to
     *
     * @param DateTime $dateTo date to
     *
     * @return self
     */
    function setDateTo(DateTime $dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Initialize collections
     */
    public function initializeCollections()
    {
        if (!($this->members instanceof Collection)) {
            $this->members = new ArrayCollection();
        }
    }
}
