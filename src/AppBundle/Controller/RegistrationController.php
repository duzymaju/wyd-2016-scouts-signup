<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\PilgrimApplicationType;
use AppBundle\Form\Type\ScoutApplicationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        return $this->render('AppBundle::registration/index.html.twig');
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
        $form = $this->createForm(new PilgrimApplicationType(), null, array(
            'action' => $this->generateUrl('registration_pilgrims'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            // @TODO: Add form data saving
            $response = $this->redirect($this->generateUrl('registration_success'));
        } else {
            $response = $this->render('AppBundle::registration/pilgrims.html.twig', array(
                'form' => $form->createView(),
            ));
        }

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
        $form = $this->createForm(new ScoutApplicationType(), null, array(
            'action' => $this->generateUrl('registration_pilgrims'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            // @TODO: Add form data saving
            $response = $this->redirect($this->generateUrl('registration_success'));
        } else {
            $response = $this->render('AppBundle::registration/scouts.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        return $response;
    }

    /**
     * Success action
     *
     * @return Response
     */
    public function successAction()
    {
        return $this->render('AppBundle::registration/success.html.twig');
    }
}
