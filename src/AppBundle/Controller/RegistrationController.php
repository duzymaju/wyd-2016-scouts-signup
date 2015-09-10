<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @return Response
     */
    public function pilgrimsAction()
    {
        return $this->render('AppBundle::registration/pilgrims.html.twig');
    }

    /**
     * Scouts action
     *
     * @return Response
     */
    public function scoutsAction()
    {
        return $this->render('AppBundle::registration/scouts.html.twig');
    }
}
