<?php

namespace Wyd2016Bundle\Controller;

use DateTime;
use Doctrine\ORM\ORMException;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Wyd2016Bundle\Entity\EntityInterface;
use Wyd2016Bundle\Entity\Pilgrim;
use Wyd2016Bundle\Entity\Repository\BaseRepositoryInterface;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Exception\RegistrationException;
use Wyd2016Bundle\Form\Type\PilgrimApplicationType;
use Wyd2016Bundle\Form\Type\VolunteerApplicationType;

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
     * Pilgrim form action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function pilgrimFormAction(Request $request)
    {
        $formType = new PilgrimApplicationType($this->get('translator'), $request->getLocale());

        $response = $this->registrationProcedure($request, $formType, new Pilgrim(),
            $this->get('wyd2016bundle.pilgrim.repository'), 'registration_pilgrim_form',
            'registration_pilgrim_confirm', 'Wyd2016Bundle::registration/pilgrim_form.html.twig',
            'Wyd2016Bundle::registration/pilgrim_email.html.twig', Pilgrim::STATUS_NOT_CONFIRMED);

        return $response;
    }

    /**
     * Volunteer form action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function volunteerFormAction(Request $request)
    {
        $formType = new VolunteerApplicationType($this->get('translator'), $request->getLocale());

        $response = $this->registrationProcedure($request, $formType, new Volunteer(),
            $this->get('wyd2016bundle.volunteer.repository'), 'registration_volunteer_form',
            'registration_volunteer_confirm', 'Wyd2016Bundle::registration/volunteer_form.html.twig',
            'Wyd2016Bundle::registration/volunteer_email.html.twig', Volunteer::STATUS_NOT_CONFIRMED);

        return $response;
    }

    /**
     * Rules action
     *
     * @return Response
     */
    public function rulesAction()
    {
        return $this->render('Wyd2016Bundle::registration/rules.html.twig');
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
        $response = $this->confirmationProcedure($this->get('wyd2016bundle.pilgrim.repository'), $hash,
            Pilgrim::STATUS_CONFIRMED);

        return $response;
    }

    /**
     * Volunteer confirm action
     *
     * @param string $hash hash
     *
     * @return Response
     */
    public function volunteerConfirmAction($hash)
    {
        $response = $this->confirmationProcedure($this->get('wyd2016bundle.volunteer.repository'), $hash,
            Volunteer::STATUS_CONFIRMED);

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
     * @param string                  $confirmRoute confirm route
     * @param string                  $formView     form view
     * @param string                  $emailView    email view
     * @param integer                 $status       status
     *
     * @return Response
     */
    protected function registrationProcedure(Request $request, FormTypeInterface $type, EntityInterface $entity,
        BaseRepositoryInterface $repository, $formRoute, $confirmRoute, $formView, $emailView, $status)
    {
        $form = $this->createForm($type, $entity, array(
            'action' => $this->generateUrl($formRoute),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = $this->generateActivationHash($entity);
            $entity->setStatus($status)
                ->setActivationHash($hash)
                ->setCreatedAt(new DateTime());

            $translator = $this->get('translator');

            $message = Swift_Message::newInstance()
                ->setSubject($translator->trans('email.title'))
                ->setFrom($this->getParameter('mailer_user'))
                ->setTo($entity->getEmail())
                ->setBody($this->renderView($emailView, array(
                    'confirmationUrl' => $this->generateUrl($confirmRoute, array(
                        'hash' => $hash,
                    ), UrlGeneratorInterface::ABSOLUTE_URL),
                )), 'text/html');

            try {
                $mailer = $this->get('mailer');
                if (!$mailer->send($message)) {
                    throw new RegistrationException('form.exception.email');
                }

                try {
                    $repository->insert($entity, true);
                } catch (ORMException $e) {
                    throw new RegistrationException('form.exception.database', 0, $e);
                }

                $this->addMessage('success.message', 'success');
                $response = $this->redirect($this->generateUrl('registration_success'));
            } catch (ExceptionInterface $e) {
                $this->addMessage($e->getMessage(), 'error');
            }
        }
        if (!isset($response)) {
            $response = $this->render($formView, array(
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
        /** @var Pilgrim|Volunteer $entity */
        $entity = $repository->findOneBy(array(
            'activationHash' => $hash,
        ));

        if (!isset($entity) || $entity->isConfirmed()) {
            $this->addMessage('confirmation.error', 'error');
        } else {
            $entity->setStatus($status);
            $repository->update($entity, true);
            $this->addMessage('confirmation.success', 'success');
        }

        return $this->render('Wyd2016Bundle::registration/confirmation.html.twig');
    }

    /**
     * Generate activation hash
     *
     * @param Pilgrim|Volunteer $entity entity
     *
     * @return string
     */
    protected function generateActivationHash(EntityInterface $entity)
    {
        $activationHash = md5(implode('-', array(
            $entity->getId(),
            $entity->getEmail(),
        )));

        return $activationHash;
    }

    /**
     * Add message
     *
     * @param string $message message
     * @param string $type    type
     *
     * @return self
     */
    protected function addMessage($message, $type = 'message')
    {
        $this->get('session')
            ->getFlashBag()
            ->add($type, $message);

        return $this;
    }
}
