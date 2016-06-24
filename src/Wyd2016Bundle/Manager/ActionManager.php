<?php

namespace Wyd2016Bundle\Manager;

use DateTime;
use Wyd2016Bundle\Entity\Action;
use Wyd2016Bundle\Entity\Repository\ActionRepository;
use Wyd2016Bundle\Entity\User;

/**
 * Manager
 */
class ActionManager
{
    /** @var ActionRepository */
    protected $actionRepository;

    /**
     * Set action repository
     *
     * @param ActionRepository $actionRepository action repository
     *
     * @return self
     */
    public function setActionRepository(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;

        return $this;
    }

    /**
     * Log
     *
     * @param string       $type     type
     * @param integer|null $objectId object ID
     * @param User|null    $user     user
     *
     * @return self
     */
    public function log($type, $objectId = null, User $user = null)
    {
        $createdAt = new DateTime();
        $action = new Action();
        $action->setType($type)
            ->setObjectId($objectId)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($createdAt);
        if (isset($user)) {
            $action->setUser($user);
        }
        $this->actionRepository->insert($action, true);

        return $this;
    }
}
