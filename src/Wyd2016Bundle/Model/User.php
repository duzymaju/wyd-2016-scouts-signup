<?php

namespace Wyd2016Bundle\Model;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * Model
 */
class User extends BaseUser
{
    /** @var integer */
    protected $id;

    /** @var string */
    protected $apiToken;

    /** @var ArrayCollection */
    protected $actions;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->initializeCollections();
    }

    /**
     * Get API token
     *
     * @return string|null
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set API token
     *
     * @param string|null $apiToken API token
     *
     * @return self
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get actions
     *
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Add action
     *
     * @param Action $action action
     *
     * @return self
     */
    public function addMember(Action $action)
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }

        return $this;
    }

    /**
     * Remove action
     *
     * @param Volunteer $action action
     *
     * @return self
     */
    public function removeMember(Action $action)
    {
        if ($this->actions->contains($action)) {
            $this->actions->removeElement($action);
        }

        return $this;
    }

    /**
     * Set actions
     *
     * @param ArrayCollection $actions actions
     *
     * @return self
     */
    public function setActions(ArrayCollection $actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Initialize collections
     */
    public function initializeCollections()
    {
        if (!($this->actions instanceof Collection)) {
            $this->actions = new ArrayCollection();
        }
    }
}
