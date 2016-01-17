<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
class Pilgrim extends ParticipantAbstract implements PersonInterface
{
    use PersonTrait;

    /** @var Group|null */
    protected $group;

    /**
     * Get group
     *
     * @return Group|null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set group
     *
     * @param Group|null $group group
     *
     * @return self
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;

        return $this;
    }
}
