<?php

namespace Wyd2016Bundle\Controller;

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
        $response = $this->registrationProcedure($request, new PilgrimApplicationType(),
            $this->get('wyd2016bundle.pilgrim_application.repository'), new PilgrimApplication(),
            'registration_pilgrims', 'Wyd2016Bundle::registration/pilgrims.html.twig',
            PilgrimApplication::STATUS_NOT_CONFIRMED);

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
        $response = $this->registrationProcedure($request, new ScoutApplicationType(),
            $this->get('wyd2016bundle.scout_application.repository'), new ScoutApplication(),
            'registration_scouts', 'Wyd2016Bundle::registration/scouts.html.twig',
            ScoutApplication::STATUS_NOT_CONFIRMED);

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
     * Registration procedure
     *
     * @param Request                 $request      request
     * @param FormTypeInterface       $type         type
     * @param BaseRepositoryInterface $repository   repository
     * @param EntityInterface         $entity       entity
     * @param string                  $formRoute    form route
     * @param string                  $view         view
     * @param integer                 $status       status
     *
     * @return Response
     */
    protected function registrationProcedure(Request $request, FormTypeInterface $type,
        BaseRepositoryInterface $repository, EntityInterface $entity, $formRoute, $view, $status)
    {
        $form = $this->createForm($type, $entity, array(
            'action' => $this->generateUrl($formRoute),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setStatus($status)
                ->setActivationHash($this->generateActivationHash($entity));
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
