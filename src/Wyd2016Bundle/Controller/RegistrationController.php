<?php

namespace Wyd2016Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Entity\PilgrimApplication;
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
        $form = $this->createForm(new PilgrimApplicationType(), new PilgrimApplication(), array(
            'action' => $this->generateUrl('registration_pilgrims'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            // @TODO: Add form data saving
            $response = $this->redirect($this->generateUrl('registration_success'));
        } else {
            $response = $this->render('Wyd2016Bundle::registration/pilgrims.html.twig', array(
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
        $form = $this->createForm(new ScoutApplicationType(), new ScoutApplication(), array(
            'action' => $this->generateUrl('registration_pilgrims'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            // @TODO: Add form data saving
            $response = $this->redirect($this->generateUrl('registration_success'));
        } else {
            $response = $this->render('Wyd2016Bundle::registration/scouts.html.twig', array(
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
        return $this->render('Wyd2016Bundle::registration/success.html.twig');
    }
}
