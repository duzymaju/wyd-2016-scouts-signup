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

        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');
        $troopRepository = $this->get('wyd2016bundle.troop.repository');
        $pilgrimRepository = $this->get('wyd2016bundle.pilgrim.repository');
        $groupRepository = $this->get('wyd2016bundle.group.repository');

        return $this->render('Wyd2016Bundle::admin/index.html.twig', array(
            'limit' => $limit,
            'lists' => array(
                'volunteers' => array(
                    'counter' => 'admin.volunteers.counter',
                    'items' => $volunteerRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_volunteer_index',
                    'routeShow' => 'admin_volunteer_show',
                    'title' => 'admin.volunteers',
                    'totalNumber' => $volunteerRepository->getTotalNumber(),
                ),
                'troops' => array(
                    'counter' => 'admin.troops.counter',
                    'items' => $troopRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_troop_index',
                    'routeShow' => 'admin_troop_show',
                    'title' => 'admin.troops',
                    'totalNumber' => $troopRepository->getTotalNumber(),
                ),
                'pilgrims' => array(
                    'counter' => 'admin.pilgrims.counter',
                    'items' => $pilgrimRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_pilgrim_index',
                    'routeShow' => 'admin_pilgrim_show',
                    'title' => 'admin.pilgrims',
                    'totalNumber' => $pilgrimRepository->getTotalNumber(),
                ),
                'groups' => array(
                    'counter' => 'admin.groups.counter',
                    'items' => $groupRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_group_index',
                    'routeShow' => 'admin_group_show',
                    'title' => 'admin.groups',
                    'totalNumber' => $groupRepository->getTotalNumber(),
                ),
            ),
            'stats' => array(
                'countries' => $volunteerRepository->countByCountries(),
                'regions' => $volunteerRepository->countByRegions(),
                'services' => $volunteerRepository->countByServices(),
            ),
        ));
    }

    /**
     * Main action
     *
     * @return Response
     */
    public function mainAction()
    {
        return $this->redirect($this->generateUrl('admin_index'));
    }
}
