<?php

namespace Wyd2016Bundle\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Entity\EntityInterface;
use Wyd2016Bundle\Entity\PilgrimApplication;
use Wyd2016Bundle\Entity\Repository\BaseRepositoryInterface;
use Wyd2016Bundle\Entity\ScoutApplication;
use Wyd2016Bundle\Form\Type\PilgrimApplicationType;
use Wyd2016Bundle\Form\Type\ScoutApplicationType;

/**
 * Controller
 */
class RegistrationController extends Controller
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('Wyd2016Bundle::registration/index.html.twig');
    }

    /**
     * Pilgrims action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function pilgrimsAction(Request $request)
    {
        $formType = new PilgrimApplicationType($this->get('translator'), $request->getLocale());

        $response = $this->registrationProcedure($request, $formType, new PilgrimApplication(),
            $this->get('wyd2016bundle.pilgrim_application.repository'), 'registration_pilgrims',
            'Wyd2016Bundle::registration/pilgrims.html.twig', PilgrimApplication::STATUS_NOT_CONFIRMED);

        return $response;
    }

    /**
     * Scouts action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function scoutsAction(Request $request)
    {
        $formType = new ScoutApplicationType($this->get('translator'));

        $response = $this->registrationProcedure($request, $formType, new ScoutApplication(),
            $this->get('wyd2016bundle.scout_application.repository'), 'registration_scouts',
            'Wyd2016Bundle::registration/scouts.html.twig', ScoutApplication::STATUS_NOT_CONFIRMED);

        return $response;
    }

    /**
     * Success action
     *
     * @return Response
     */
    public function successAction()
    {
        return $this->render('Wyd2016Bundle::registration/success.html.twig');
    }

    /**
     * Pilgrim confirm action
     * 
     * @param string $hash hash
     *
     * @return Response
     */
    public function pilgrimConfirmAction($hash)
    {
        $response = $this->confirmationProcedure($this->get('wyd2016bundle.pilgrim_application.repository'), $hash,
            PilgrimApplication::STATUS_CONFIRMED);

        return $response;
    }

    /**
     * Scout confirm action
     *
     * @param string $hash hash
     *
     * @return Response
     */
    public function scoutConfirmAction($hash)
    {
        $response = $this->confirmationProcedure($this->get('wyd2016bundle.scout_application.repository'), $hash,
            ScoutApplication::STATUS_CONFIRMED);

        return $response;
    }

    /**
     * Registration procedure
     *
     * @param Request                 $request      request
     * @param FormTypeInterface       $type         type
     * @param EntityInterface         $entity       entity
     * @param BaseRepositoryInterface $repository   repository
     * @param string                  $formRoute    form route
     * @param string                  $view         view
     * @param integer                 $status       status
     *
     * @return Response
     */
    protected function registrationProcedure(Request $request, FormTypeInterface $type, EntityInterface $entity,
        BaseRepositoryInterface $repository, $formRoute, $view, $status)
    {
        $form = $this->createForm($type, $entity, array(
            'action' => $this->generateUrl($formRoute),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setStatus($status)
                ->setActivationHash($this->generateActivationHash($entity))
                ->setCreatedAt(new DateTime());
            $repository->insert($entity, true);

            $response = $this->redirect($this->generateUrl('registration_success'));
        } else {
            $response = $this->render($view, array(
                'form' => $form->createView(),
            ));
        }

        return $response;
    }

    /**
     * Confirmation procedure
     *
     * @param BaseRepositoryInterface $repository repository
     * @param string                  $hash       hash
     * @param integer                 $status     status
     *
     * @return Response
     */
    protected function confirmationProcedure(BaseRepositoryInterface $repository, $hash, $status)
    {
        /** @var PilgrimApplication|ScoutApplication $entity */
        $entity = $repository->findOneBy(array(
            'activationHash' => $hash,
        ));

        if (!isset($entity) || $entity->isConfirmed()) {
            $success = false;
        } else {
            $entity->setStatus($status);
            $repository->update($entity, true);
            $success = true;
        }

        return $this->render('Wyd2016Bundle::registration/confirmation.html.twig', array(
            'success' => $success,
        ));
    }

    /**
     * Generate activation hash
     *
     * @param PilgrimApplication|ScoutApplication $entity entity
     *
     * @return string
     */
    protected function generateActivationHash(EntityInterface $entity)
    {
        $activationHash = md5(implode('-', array(
            $entity->getId(),
            $entity->getMail(),
        )));

        return $activationHash;
    }
}
