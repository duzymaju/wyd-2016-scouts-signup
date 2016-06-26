<?php

namespace Wyd2016Bundle\Controller\Admin;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Entity\Repository\TroopRepository;
use Wyd2016Bundle\Entity\Troop;

/**
 * Admin controller
 */
class TroopController extends AbstractController
{
    /**
     * Index action
     *
     * @param Request $request request
     * @param integer $pageNo  page no
     *
     * @return Response
     */
    public function indexAction(Request $request, $pageNo)
    {
        $criteria = array();

        $registrationLists = $this->get('wyd2016bundle.registration.lists');

        $status = null;
        if (count($criteria) == 0) {
            $status = $request->query->getInt('status', -1);
            if ($status > -1 && $registrationLists->getStatus($status)) {
                $criteria['status'] = $status;
            } else {
                $status = null;
            }
        }

        /** @var Paginator $troops */
        $troops = $this->getRepository()
            ->getPackOrException($pageNo, $this->getParameter('wyd2016.admin.pack_size'), $criteria, array(
                'createdAt' => 'DESC',
            ));

        return $this->render('Wyd2016Bundle::admin/troop/index.html.twig', array(
            'criteria' => $criteria,
            'troops' => $troops->setRouteName('admin_troop_index'),
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
        /** @var Troop $troop */
        $troop = $this->getRepository()
            ->findOneByOrException(array(
                'id' => $id,
            ));

        $response = $this->sendReminderIfRequested($troop, $request, $this->getRepository(),
            'registration_troop_confirm', 'Wyd2016Bundle::admin/troop/email.html.twig');

        if (!isset($response)) {
            $response = $this->render('Wyd2016Bundle::admin/troop/show.html.twig', array(
                'ageLimit' => $this->getParameter('wyd2016.age.limit'),
                'isReminderSendingPossible' => $this->isReminderSendingPossible($troop),
                'troop' => $troop,
            ));
        }

        return $response;
    }

    /**
     * Get repository
     *
     * @return TroopRepository
     */
    protected function getRepository()
    {
        $repository = $this->get('wyd2016bundle.troop.repository');

        return $repository;
    }
}
