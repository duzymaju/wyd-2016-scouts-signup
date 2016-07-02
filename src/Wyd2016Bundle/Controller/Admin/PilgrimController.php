<?php

namespace Wyd2016Bundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Pilgrim;
use Wyd2016Bundle\Entity\Repository\PilgrimRepository;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\PilgrimEditType;
use Wyd2016Bundle\Model\Action;
use Wyd2016Bundle\Twig\WydExtension;

/**
 * Admin controller
 */
class PilgrimController extends AbstractController
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
        /** @var Paginator $pilgrims */
        $pilgrims = $this->getRepository()
            ->getPackOrException($pageNo, $this->getParameter('wyd2016.admin.pack_size'), array(), array(
                'createdAt' => 'DESC',
            ), array(
                'g' => 'group',
            ));

        return $this->render('Wyd2016Bundle::admin/pilgrim/index.html.twig', array(
            'pilgrims' => $pilgrims->setRouteName('admin_pilgrim_index'),
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
        /** @var Pilgrim $pilgrim */
        $pilgrim = $this->getRepository()
            ->findOneByOrException(array(
                'id' => $id,
            ));

        return $this->render('Wyd2016Bundle::admin/pilgrim/show.html.twig', array(
            'ageLimit' => $this->getParameter('wyd2016.age.limit'),
            'pilgrim' => $pilgrim,
        ));
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
        /** @var PilgrimRepository $pilgrimRepository */
        $pilgrimRepository = $this->get('wyd2016bundle.pilgrim.repository');
        /** @var Pilgrim $pilgrim */
        $pilgrim = $pilgrimRepository->findOneByOrException(array(
            'id' => $id,
        ));

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new PilgrimEditType($translator, $registrationLists);

        $form = $this->createForm($formType, $pilgrim, array(
            'action' => $this->generateUrl('admin_pilgrim_edit', array(
                'id' => $id,
            )),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $pilgrim->setUpdatedAt(new DateTime());
                $pilgrimRepository->update($pilgrim, true);

                $this->get('wyd2016bundle.manager.action')
                    ->log(Action::TYPE_UPDATE_PILGRIM_DATA, $pilgrim->getId(), $this->getUser());

                $this->addMessage('admin.edit.success', 'success');
                $response = $this->softRedirect($this->generateUrl('admin_pilgrim_show', array(
                    'id' => $id,
                )));
            } catch (ExceptionInterface $e) {
                unset($e);
                $this->addMessage('form.exception.database', 'error');
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::admin/pilgrim/edit.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        return $response;
    }

    /**
     * List action
     *
     * @return Response
     */
    public function listAction()
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var WydExtension $filters */
        $filters = $this->get('wyd2016bundle.twig_extension.wyd');
        $pilgrims = $this->getRepository()
            ->getAllOrderedBy(array(
                'createdAt' => 'DESC',
            ));

        $data = array();
        $data[] = array(
            $translator->trans('form.status'),
            $translator->trans('form.first_name'),
            $translator->trans('form.last_name'),
            $translator->trans('form.address'),
            $translator->trans('form.phone'),
            $translator->trans('form.email'),
            $translator->trans('form.country'),
            $translator->trans('form.birth_date'),
            $translator->trans('admin.age_at_limit', [
                '%date%' => $this->getParameter('wyd2016.age.limit'),
            ]),
            $translator->trans('form.sex'),
            $translator->trans('form.shirtSize'),
            $translator->trans('form.dates'),
            $translator->trans('form.comments'),
            $translator->trans('form.group_name'),
            $translator->trans('admin.created_at'),
        );
        foreach ($pilgrims as $pilgrim) {
            /** @var Pilgrim $pilgrim */
            $data[] = array(
                $filters->statusNameFilter($pilgrim->getStatus()),
                $pilgrim->getFirstName(),
                $pilgrim->getLastName(),
                $pilgrim->getAddress(),
                $pilgrim->getPhone(),
                $pilgrim->getEmail(),
                $filters->localizedCountryFilter($pilgrim->getCountry()),
                $pilgrim->getBirthDate()
                    ->format('Y-m-d'),
                $filters->ageAtLimitFilter($pilgrim->getBirthDate()),
                $filters->sexNameFilter($pilgrim->getSex()),
                $pilgrim->getShirtSize() > 0 ? $filters->shirtSizeNameFilter($pilgrim->getShirtSize()) : '-',
                $filters->pilgrimDateFilter($pilgrim->getDatesId()),
                empty($pilgrim->getComments()) ? '-' : $pilgrim->getComments(),
                $pilgrim->getGroup() ? $pilgrim->getGroup()
                    ->getName() : '-',
                $pilgrim->getCreatedAt()
                    ->format('Y-m-d'),
            );
        }

        return $this->getCsvResponse($data, 'pilgrim_list');
    }

    /**
     * Get repository
     *
     * @return PilgrimRepository
     */
    protected function getRepository()
    {
        $repository = $this->get('wyd2016bundle.pilgrim.repository');

        return $repository;
    }
}
