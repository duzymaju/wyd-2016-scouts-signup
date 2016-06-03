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
}
