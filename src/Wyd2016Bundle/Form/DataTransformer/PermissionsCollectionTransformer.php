<?php

namespace Wyd2016Bundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Wyd2016Bundle\Entity\Permission;

/**
 * Data transformer
 */
class PermissionsCollectionTransformer implements DataTransformerInterface
{
    /**
     * Transform
     *
     * @param Collection $permissions permissions
     *
     * @return array
     */
    public function transform($permissions)
    {
        $array = array();
        foreach ($permissions as $permission) {
            /** @var Permission $permission */
            $array[] = $permission->getId();
        }

        return $array;
    }

    /**
     * Reverse transform
     *
     * @param array $array
     *
     * @return ArrayCollection
     */
    public function reverseTransform($array)
    {
        $permissions = new ArrayCollection();
        foreach ((array) $array as $id) {
            if ($id > 0) {
                $permission = new Permission();
                $permission->setId($id);
                $permissions->add($permission);
            }
        }

        return $permissions;
    }
}
