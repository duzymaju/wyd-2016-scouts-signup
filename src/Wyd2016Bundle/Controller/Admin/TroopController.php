<?php

namespace Wyd2016Bundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Repository\TroopRepository;
use Wyd2016Bundle\Entity\Troop;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\TroopEditType;
use Wyd2016Bundle\Model\Action;

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
        $criteriaSettings = array(
            'status' => array(
                'getter' => 'getStatus',
                'lowestValue' => 0,
            ),
        );
        $criteria = $this->getCriteria($request->query, $criteriaSettings);

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
     * Edit action
     *
     * @param Request $request request
     * @param integer $id      ID
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var TroopRepository $troopRepository */
        $troopRepository = $this->get('wyd2016bundle.troop.repository');
        /** @var Troop $troop */
        $troop = $troopRepository->findOneByOrException(array(
            'id' => $id,
        ));

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new TroopEditType($translator, $registrationLists);

        $form = $this->createForm($formType, $troop, array(
            'action' => $this->generateUrl('admin_troop_edit', array(
                'id' => $id,
            )),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $troop->setUpdatedAt(new DateTime());
            try {
                $troopRepository->update($troop, true);
                $this->get('wyd2016bundle.manager.action')
                    ->log(Action::TYPE_UPDATE_TROOP_DATA, $troop->getId(), $this->getUser());
                $this->addMessage('admin.edit.success', 'success');
                $response = $this->softRedirect($this->generateUrl('admin_troop_show', array(
                    'id' => $id,
                )));
            } catch (ExceptionInterface $e) {
                unset($e);
                $this->addMessage('form.exception.database', 'error');
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::admin/troop/edit.html.twig', array(
                'form' => $form->createView(),
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
