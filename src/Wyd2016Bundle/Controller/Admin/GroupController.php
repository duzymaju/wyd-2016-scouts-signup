<?php

namespace Wyd2016Bundle\Controller\Admin;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Entity\Repository\GroupRepository;
use Wyd2016Bundle\Entity\Group;

/**
 * Admin controller
 */
class GroupController extends Controller
{
    /**
     * Index action
     *
     * @param integer $pageNo page no
     *
     * @return Response
     */
    public function indexAction($pageNo)
    {
        /** @var Paginator $groups */
        $groups = $this->getRepository()
            ->getPackOrException($pageNo, $this->getParameter('wyd2016.admin.pack_size'), array(), array(
                'createdAt' => 'DESC',
            ));

        return $this->render('Wyd2016Bundle::admin/group/index.html.twig', array(
            'groups' => $groups->setRouteName('admin_group_index'),
        ));
    }

    /**
     * Show action
     *
     * @param integer $id ID
     *
     * @return Response
     */
    public function showAction($id)
    {
        /** @var Group $group */
        $group = $this->getRepository()
            ->findOneByOrException(array(
                'id' => $id,
            ));

        return $this->render('Wyd2016Bundle::admin/group/show.html.twig', array(
            'ageLimit' => $this->getParameter('wyd2016.age.limit'),
            'group' => $group,
        ));
    }

    /**
     * Get repository
     *
     * @return GroupRepository
     */
    protected function getRepository()
    {
        $repository = $this->get('wyd2016bundle.group.repository');

        return $repository;
    }
}
