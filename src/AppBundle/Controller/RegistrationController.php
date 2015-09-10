<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * Guests action
     *
     * @return Response
     */
    public function guestsAction()
    {
        return $this->render('AppBundle::registration/guests.html.twig');
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
