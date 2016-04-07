<?php

namespace Wyd2016Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller
 */
class AdminController extends Controller
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction()
    {
        $criteria = array();
        $orderBy = array(
            'createdAt' => 'DESC',
        );
        $limit = 5;

        return $this->render('Wyd2016Bundle::admin/index.html.twig', array(
            'limit' => $limit,
            'lists' => array(
                'volunteers' => array(
                    'counter' => 'admin.volunteers.counter',
                    'items' => $this->get('wyd2016bundle.volunteer.repository')
                        ->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_volunteer_index',
                    'routeShow' => 'admin_volunteer_show',
                    'title' => 'admin.volunteers',
                ),
                'troops' => array(
                    'counter' => 'admin.troops.counter',
                    'items' => $this->get('wyd2016bundle.troop.repository')
                        ->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_troop_index',
                    'routeShow' => 'admin_troop_show',
                    'title' => 'admin.troops',
                ),
                'pilgrims' => array(
                    'counter' => 'admin.pilgrims.counter',
                    'items' => $this->get('wyd2016bundle.pilgrim.repository')
                        ->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_pilgrim_index',
                    'routeShow' => 'admin_pilgrim_show',
                    'title' => 'admin.pilgrims',
                ),
                'groups' => array(
                    'counter' => 'admin.groups.counter',
                    'items' => $this->get('wyd2016bundle.group.repository')
                        ->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_group_index',
                    'routeShow' => 'admin_group_show',
                    'title' => 'admin.groups',
                ),
            ),
        ));
    }
}