<?php

namespace Wyd2016Bundle\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Wyd2016Bundle\Entity\EntityInterface;
use Wyd2016Bundle\Entity\Language;
use Wyd2016Bundle\Entity\Pilgrim;
use Wyd2016Bundle\Entity\Repository\BaseRepositoryInterface;
use Wyd2016Bundle\Entity\Repository\TroopRepository;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Entity\Troop;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Exception\RegistrationException;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\PilgrimType;
use Wyd2016Bundle\Form\Type\TroopType;
use Wyd2016Bundle\Form\Type\VolunteerType;

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
        $formType = new PilgrimType($this->get('translator'), $request->getLocale(),
            $this->get('wyd2016bundle.registration.lists'));

        $response = $this->standardRegistrationProcedure($request, $formType, new Pilgrim(),
            $this->get('wyd2016bundle.pilgrim.repository'), 'registration_pilgrim_form',
            'registration_pilgrim_confirm', 'Wyd2016Bundle::registration/pilgrim_form.html.twig',
            'Wyd2016Bundle::registration/pilgrim_email.html.twig', Pilgrim::STATUS_NOT_CONFIRMED);

        return $response;
    }

    /**
     * Troop form action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function troopFormAction(Request $request)
    {
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new TroopType($this->get('translator'), $request->getLocale(), $registrationLists);

        $troop = new Troop();
        $leader = new Volunteer();
        $leader->setTroop($troop);
        $troop->setLeader($leader)
            ->addMember($leader);
        for ($i = 1; $i < $this->getParameter('wyd2016.troop_size'); $i++) {
            $member = new Volunteer();
            $member->setTroop($troop);
            $troop->addMember($member);
        }
        $form = $this->createForm($formType, $troop, array(
            'action' => $this->generateUrl('registration_troop_form'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = $this->generateActivationHash($leader->getEmail());
            $createdAt = new DateTime();
            $troop->setStatus(Troop::STATUS_NOT_CONFIRMED)
                ->setActivationHash($hash)
                ->setCreatedAt($createdAt);
            foreach ($troop->getMembers() as $member) {
                /** @var Volunteer $member */
                $member->setStatus(Troop::STATUS_NOT_CONFIRMED)
                    ->setActivationHash($this->generateActivationHash($member->getEmail()))
                    ->setCountry($form->get('country')->getData())
                    ->setServiceMainId($registrationLists::SERVICE_UNDERAGE)
                    ->setPermissions($form->get('permissions')->getData())
                    ->setProfession($form->get('profession')->getData())
                    ->setOwnTent($troop->hasOwnTent())
                    ->setDatesId($troop->getDatesId())
                    ->setCreatedAt($createdAt);
                // Adds region and district to Polish volunteer or removes grade from foreigner
                if ($member->getPesel()) {
                    $member->setRegionId($form->get('regionId')->getData())
                        ->setDistrictId($form->get('districtId')->getData());
                } else {
                    $member->setGradeId();
                }
            }

            try {
                $this->mailSendingProcedure($leader->getEmail(), 'registration_troop_confirm',
                    'Wyd2016Bundle::registration/troop_email.html.twig', $hash);

                try {
                    $this->get('wyd2016bundle.troop.repository')
                        ->insert($troop, true);
                } catch (Exception $e) {
                    throw new RegistrationException('form.exception.database', 0, $e);
                }

                $this->addMessage('success.message', 'success');
                $response = $this->redirect($this->generateUrl('registration_success'));
            } catch (ExceptionInterface $e) {
                $this->addMessage($e->getMessage(), 'error');
            }
        }
        if (!isset($response)) {
            $response = $this->render('Wyd2016Bundle::registration/troop_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }

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
        $formType = new VolunteerType($this->get('translator'), $request->getLocale(),
            $this->get('wyd2016bundle.registration.lists'));

        $response = $this->standardRegistrationProcedure($request, $formType, new Volunteer(),
            $this->get('wyd2016bundle.volunteer.repository'), 'registration_volunteer_form',
            'registration_volunteer_confirm', 'Wyd2016Bundle::registration/volunteer_form.html.twig',
            'Wyd2016Bundle::registration/volunteer_email.html.twig', Volunteer::STATUS_NOT_CONFIRMED);

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
        $response = $this->confirmationProcedure($this->get('wyd2016bundle.pilgrim.repository'), $hash,
            Pilgrim::STATUS_CONFIRMED);

        return $response;
    }

    /**
     * Troop confirm action
     *
     * @param string $hash hash
     *
     * @return Response
     */
    public function troopConfirmAction($hash)
    {
        /** @var TroopRepository $troopRepository */
        $troopRepository = $this->get('wyd2016bundle.troop.repository');
        /** @var VolunteerRepository $volunteerRepository */
        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');

        /** @var Troop $troop */
        $troop = $troopRepository->findOneBy(array(
            'activationHash' => $hash,
        ));

        if (!isset($troop) || $troop->isConfirmed()) {
            $this->addMessage('confirmation.error', 'error');
        } else {
            foreach ($troop->getMembers() as $member) {
                /** @var Volunteer $member */
                if (!$member->isConfirmed()) {
                    $member->setStatus(Volunteer::STATUS_CONFIRMED);
                    $volunteerRepository->update($member);
                }
            }
            $volunteerRepository->flush();
            $troop->setStatus(Troop::STATUS_CONFIRMED);
            $troopRepository->update($troop, true);
            $this->addMessage('confirmation.success', 'success');
        }

        return $this->render('Wyd2016Bundle::registration/confirmation.html.twig');
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
     * Standard registration procedure
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
    protected function standardRegistrationProcedure(Request $request, FormTypeInterface $type, EntityInterface $entity,
        BaseRepositoryInterface $repository, $formRoute, $confirmRoute, $formView, $emailView, $status)
    {
        $form = $this->createForm($type, $entity, array(
            'action' => $this->generateUrl($formRoute),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = $this->generateActivationHash($entity->getEmail());
            $entity->setStatus($status)
                ->setActivationHash($hash)
                ->setCreatedAt(new DateTime());

            // Removes grade, region and district from foreign volunteer
            if ($entity instanceof Volunteer && !$entity->getPesel()) {
                $entity->setGradeId()
                    ->setRegionId()
                    ->setDistrictId();
            }

            try {
                $this->mailSendingProcedure($entity->getEmail(), $confirmRoute, $emailView, $hash);

                try {
                    // Save volunteer before save its languages because of Doctrine requirements
                    if ($entity instanceof Volunteer) {
                        $languages = $entity->getLanguages();
                        foreach ($languages as $language) {
                            /** @var Language $language */
                            $language->setVolunteer($entity);
                        }
                        $entity->setLanguages(new ArrayCollection());
                        $repository->insert($entity, true);
                        $entity->setLanguages($languages);
                    }

                    $repository->insert($entity, true);
                } catch (Exception $e) {
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
     * Mail sending procedure
     *
     * @param string $email        e-mail
     * @param string $confirmRoute confirm route
     * @param string $emailView    email view
     * @param string $hash         hash
     *
     * @throws RegistrationException
     */
    protected function mailSendingProcedure($email, $confirmRoute, $emailView, $hash)
    {
        $translator = $this->get('translator');

        $message = Swift_Message::newInstance()
            ->setSubject($translator->trans('email.title'))
            ->setFrom($this->getParameter('mailer_user'))
            ->setTo($email)
            ->setBody($this->renderView($emailView, array(
                'confirmationUrl' => $this->generateUrl($confirmRoute, array(
                    'hash' => $hash,
                ), UrlGeneratorInterface::ABSOLUTE_URL),
            )), 'text/html');

        $mailer = $this->get('mailer');
        if (!$mailer->send($message)) {
            throw new RegistrationException('form.exception.email');
        }
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
     * @param string $email e-mail
     *
     * @return string
     */
    protected function generateActivationHash($email)
    {
        $activationHash = md5(implode('-', array(
            $email,
            time(),
            rand(10000, 99999),
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
