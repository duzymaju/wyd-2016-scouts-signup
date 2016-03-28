<?php

namespace Wyd2016Bundle\Controller\Admin;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Entity\Volunteer;

/**
 * Admin controller
 */
class VolunteerController extends AbstractController
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
        /** @var Paginator $volunteers */
        $volunteers = $this->getRepository()
            ->getPackOrException($pageNo, $this->getParameter('wyd2016.admin.pack_size'), array(), array(
                'createdAt' => 'DESC',
            ));

        return $this->render('Wyd2016Bundle::admin/volunteer/index.html.twig', array(
            'volunteers' => $volunteers->setRouteName('admin_volunteer_index'),
        ));
    }

    /**
     * Show action
     *
     * @param Request $request request
     * @param integer $id      ID
     *
     * @return Response
     */
    public function showAction(Request $request, $id)
    {
        /** @var Volunteer $volunteer */
        $volunteer = $this->getRepository()
            ->findOneByOrException(array(
                'id' => $id,
            ));

        $response = $this->sendReminderIfRequested($volunteer, $request, $this->getRepository(),
            'registration_volunteer_confirm', 'Wyd2016Bundle::admin/volunteer/email.html.twig');

        if (!isset($response)) {
            $response = $this->render('Wyd2016Bundle::admin/volunteer/show.html.twig', array(
                'ageLimit' => $this->getParameter('wyd2016.age.limit'),
                'isReminderSendingPossible' => $this->isReminderSendingPossible($volunteer) && !$volunteer->getTroop(),
                'volunteer' => $volunteer,
            ));
        }

        return $response;
    }

    /**
     * Get repository
     *
     * @return VolunteerRepository
     */
    protected function getRepository()
    {
        $repository = $this->get('wyd2016bundle.volunteer.repository');

        return $repository;
    }
}
