<?php

namespace Wyd2016Bundle\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Wyd2016Bundle\Entity\Language;
use Wyd2016Bundle\Entity\Pilgrim;
use Wyd2016Bundle\Entity\Repository\BaseRepositoryInterface;
use Wyd2016Bundle\Entity\Repository\TroopRepository;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Entity\Troop;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Exception\RegistrationException;
use Wyd2016Bundle\Model\PersonInterface;
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

        $pilgrim = new Pilgrim();
        $form = $this->createForm($formType, $pilgrim, array(
            'action' => $this->generateUrl('registration_pilgrim_form'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = $this->generateActivationHash($pilgrim->getEmail());
            $pilgrim->setStatus(Pilgrim::STATUS_NOT_CONFIRMED)
                ->setActivationHash($hash)
                ->setCreatedAt(new DateTime());

            // Validates age
            $this->validateAge($pilgrim, $form->get('birthDate'), 'wyd2016.age.min_adult');

            if ($form->isValid()) {
                try {
                    $this->mailSendingProcedure($pilgrim->getEmail(), 'registration_pilgrim_confirm',
                        'Wyd2016Bundle::registration/pilgrim_email.html.twig', $hash);

                    try {
                        $this->get('wyd2016bundle.pilgrim.repository')
                            ->insert($pilgrim, true);
                    } catch (Exception $e) {
                        throw new RegistrationException('form.exception.database', 0, $e);
                    }

                    $this->addMessage('success.message', 'success');
                    $response = $this->redirect($this->generateUrl('registration_success'));
                } catch (ExceptionInterface $e) {
                    $this->addMessage($e->getMessage(), 'error');
                }
            }
        }
        if (!isset($response)) {
            $response = $this->render('Wyd2016Bundle::registration/pilgrim_form.html.twig', array(
                'form' => $form->createView(),
            ));
        }

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
        /** @var TranslatorInterface */
        $translator = $this->get('translator');

        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new TroopType($translator, $request->getLocale(), $registrationLists);

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
            $languages = new ArrayCollection();
            foreach ($form->get('languages')->getData() as $slug) {
                $language = new Language();
                $language->setVolunteer($leader)
                    ->setSlug($slug);
                $languages->add($language);
            }
            $isPolish = $this->isPolish($form);
            $usedEmails = array();
            $usedPesels = array();
            foreach ($troop->getMembers() as $i => $member) {
                /** @var Volunteer $member */
                $isLeader = $member === $troop->getLeader();
                // Copies data from form to each volunteer
                $member->setStatus(Troop::STATUS_NOT_CONFIRMED)
                    ->setActivationHash($this->generateActivationHash($member->getEmail()))
                    ->setCountry($form->get('country')->getData())
                    ->setServiceMainId($registrationLists::SERVICE_UNDERAGE)
                    ->setDatesId($troop->getDatesId())
                    ->setCreatedAt($createdAt);
                if ($isLeader && $form->has('permissions')) {
                    $member->setPermissions($form->get('permissions')->getData());
                }
                if ($isLeader && $form->has('profession')) {
                    $member->setProfession($form->get('profession')->getData());
                }
                // Adds region and district to Polish volunteer or removes grade from foreigner
                if ($isPolish) {
                    $member->setRegionId($form->get('regionId')->getData())
                        ->setDistrictId($form->get('districtId')->getData());
                } else {
                    $member->setGradeId();
                }

                /** @var FormInterface $memberView */
                $memberView = $form->get('members')->get($i);
                // Validates e-mail duplication
                if (in_array($member->getEmail(), $usedEmails)) {
                    $memberView->get('email')
                        ->addError(new FormError($translator->trans('form.error.email_duplicated')));
                }
                $usedEmails[] = $member->getEmail();
                // Validates age
                $this->validateAge($member, $memberView->get($isPolish ? 'pesel' : 'birthDate'),
                    $isLeader ? 'wyd2016.age.min_adult' : 'wyd2016.age.min_member');
                if ($isPolish) {
                    // Validates PESEL existance
                    if ($member->getPesel() == null) {
                        $memberView->get('pesel')
                            ->addError(new FormError($translator->trans('form.error.pesel_empty')));
                    }
                    // Validates PESEL duplication
                    if (in_array($member->getPesel(), $usedPesels)) {
                        $memberView->get('pesel')
                            ->addError(new FormError($translator->trans('form.error.pesel_duplicated')));
                    }
                    $usedPesels[] = $member->getPesel();
                    if ($isLeader) {
                        // Validates leader grade
                        if ($member->getGradeId() == $registrationLists::GRADE_NO) {
                            $memberView->get('gradeId')
                                ->addError(new FormError($translator->trans('form.error.grade_inproper')));
                        }
                        // Validates structure
                        // For leader only to check it only once
                        $this->validateStructure($member, $form->get('districtId'));
                    }
                }
            }

            if ($form->isValid()) {
                try {
                    $this->mailSendingProcedure($leader->getEmail(), 'registration_troop_confirm',
                        'Wyd2016Bundle::registration/troop_email.html.twig', $hash);

                    try {
                        $this->get('wyd2016bundle.troop.repository')
                            ->insert($troop, true);

                        // Add languages to leader after save him because of Doctrine requirements
                        $leader->setLanguages($languages);
                        $this->get('wyd2016bundle.volunteer.repository')
                            ->insert($leader, true);
                    } catch (Exception $e) {
                        throw new RegistrationException('form.exception.database', 0, $e);
                    }

                    $this->addMessage('success.message', 'success');
                    $response = $this->redirect($this->generateUrl('registration_success'));
                } catch (ExceptionInterface $e) {
                    $this->addMessage($e->getMessage(), 'error');
                }
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
        /** @var TranslatorInterface */
        $translator = $this->get('translator');

        $formType = new VolunteerType($this->get('translator'), $request->getLocale(),
            $this->get('wyd2016bundle.registration.lists'));

        $volunteer = new Volunteer();
        $form = $this->createForm($formType, $volunteer, array(
            'action' => $this->generateUrl('registration_volunteer_form'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = $this->generateActivationHash($volunteer->getEmail());
            $isPolish = $this->isPolish($form);
            $volunteer->setStatus(Volunteer::STATUS_NOT_CONFIRMED)
                ->setActivationHash($hash)
                ->setCreatedAt(new DateTime());

            // Removes grade, region and district from foreign volunteer
            if (!$isPolish) {
                $volunteer->setGradeId()
                    ->setRegionId()
                    ->setDistrictId();
            }

            // Validates age
            $this->validateAge($volunteer, $form->get($isPolish ? 'pesel' : 'birthDate'), 'wyd2016.age.min_adult');
            // Validates services
            if ($volunteer->getServiceMainId() == $volunteer->getServiceExtraId()) {
                $form->get('serviceExtraId')
                    ->addError(new FormError($translator->trans('form.error.services_duplicated')));
            }
            if ($isPolish) {
                // Validates PESEL existance
                if ($volunteer->getPesel() == null) {
                    $form->get('pesel')
                        ->addError(new FormError($translator->trans('form.error.pesel_empty')));
                }
                // Validates structure
                $this->validateStructure($volunteer, $form->get('districtId'));
            }

            if ($form->isValid()) {
                try {
                    $this->mailSendingProcedure($volunteer->getEmail(), 'registration_volunteer_confirm',
                        'Wyd2016Bundle::registration/volunteer_email.html.twig', $hash);

                    try {
                        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');

                        // Save volunteer before save its languages because of Doctrine requirements
                        $languages = $volunteer->getLanguages();
                        foreach ($languages as $language) {
                            /** @var Language $language */
                            $language->setVolunteer($volunteer);
                        }
                        $volunteer->setLanguages(new ArrayCollection());
                        $volunteerRepository->insert($volunteer, true);
                        $volunteer->setLanguages($languages);

                        $volunteerRepository->insert($volunteer, true);
                    } catch (Exception $e) {
                        throw new RegistrationException('form.exception.database', 0, $e);
                    }

                    $this->addMessage('success.message', 'success');
                    $response = $this->redirect($this->generateUrl('registration_success'));
                } catch (ExceptionInterface $e) {
                    $this->addMessage($e->getMessage(), 'error');
                }
            }
        }
        if (!isset($response)) {
            $response = $this->render('Wyd2016Bundle::registration/volunteer_form.html.twig', array(
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
     * Validate age
     *
     * @param PersonInterface $person          person
     * @param FormInterface   $ageField        age field
     * @param string          $minAgeParamName min age param name
     */
    protected function validateAge(PersonInterface $person, FormInterface $ageField, $minAgeParamName)
    {
        /** @var TranslatorInterface */
        $translator = $this->get('translator');

        $ageMin = $this->getParameter($minAgeParamName);
        $ageMax = $this->getParameter('wyd2016.age.max');
        $ageLimit = new DateTime($this->getParameter('wyd2016.age.limit'));

        $age = (integer) $person->getBirthDate()
            ->diff($ageLimit)
            ->format('%y');
        if ($age < $ageMin) {
            $ageField->addError(new FormError($translator->trans('form.error.age_too_low', array(
                '%age%' => $ageMin,
            ))));
        } elseif ($age > $ageMax) {
            $ageField->addError(new FormError($translator->trans('form.error.age_too_high', array(
                '%age%' => $ageMax,
            ))));
        }
    }

    /**
     * Validate structure
     *
     * @param Volunteer     $volunteer     volunteer
     * @param FormInterface $districtField district field
     */
    protected function validateStructure(Volunteer $volunteer, FormInterface $districtField)
    {
        /** @var TranslatorInterface */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');

        if (!$registrationLists->regionContainsDistrict($volunteer->getRegionId(), $volunteer->getDistrictId())) {
            $districtField->addError(new FormError($translator->trans('form.error.district_invalid')));
        }
    }

    /**
     * Is Polish
     *
     * @param FormInterface $form form
     *
     * @return boolean
     */
    protected function isPolish(FormInterface $form)
    {
        $isPolish = strtolower($form->get('country')->getData()) == RegistrationLists::COUNTRY_POLAND;

        return $isPolish;
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
