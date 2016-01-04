<?php

namespace Wyd2016Bundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
class UserRepository extends EntityRepository implements BaseRepositoryInterface
{
    use BaseRepositoryTrait;
}
