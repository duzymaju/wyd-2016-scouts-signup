<?php

namespace Wyd2016Bundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Group;
use Wyd2016Bundle\Entity\Repository\GroupRepository;
use Wyd2016Bundle\Entity\Repository\PilgrimRepository;
use Wyd2016Bundle\Exception\EditFormException;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\GroupEditType;
use Wyd2016Bundle\Manager\ActionManager;
use Wyd2016Bundle\Model\Action;

/**
 * Admin controller
 */
class GroupController extends AbstractController
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
     * Edit action
     *
     * @param Request $request request
     * @param integer $id      ID
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var GroupRepository $groupRepository */
        $groupRepository = $this->get('wyd2016bundle.group.repository');
        /** @var Group $group */
        $group = $groupRepository->findOneByOrException(array(
            'id' => $id,
        ));
        $originalStatus = $group->getStatus();

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new GroupEditType($translator, $registrationLists);

        $form = $this->createForm($formType, $group, array(
            'action' => $this->generateUrl('admin_group_edit', array(
                'id' => $id,
            )),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->prepareBandToUpdate($group, $this->get('wyd2016bundle.pilgrim.repository'), $originalStatus,
                    'admin.edit.group.member_status_error');
                $groupRepository->update($group, true);

                /** @var ActionManager $actionManager */
                $actionManager = $this->get('wyd2016bundle.manager.action');
                foreach ($group->getMembers() as $pilgrim) {
                    $actionManager->log(Action::TYPE_UPDATE_PILGRIM_DATA, $pilgrim->getId(), $this->getUser());
                }
                $actionManager->log(Action::TYPE_UPDATE_GROUP_DATA, $group->getId(), $this->getUser());

                $this->addMessage('admin.edit.success', 'success');
                $response = $this->softRedirect($this->generateUrl('admin_group_show', array(
                    'id' => $id,
                )));
            } catch (EditFormException $e) {
                $this->addMessage($e->getMessage(), 'error');
            } catch (ExceptionInterface $e) {
                unset($e);
                $this->addMessage('form.exception.database', 'error');
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::admin/group/edit.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        return $response;
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
