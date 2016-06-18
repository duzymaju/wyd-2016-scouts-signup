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
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Group;
use Wyd2016Bundle\Entity\Language;
use Wyd2016Bundle\Entity\Permission;
use Wyd2016Bundle\Entity\Pilgrim;
use Wyd2016Bundle\Entity\Repository\BaseRepositoryInterface;
use Wyd2016Bundle\Entity\Troop;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Exception\RegistrationException;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\GroupType;
use Wyd2016Bundle\Form\Type\PilgrimType;
use Wyd2016Bundle\Form\Type\TroopType;
use Wyd2016Bundle\Form\Type\VolunteerType;
use Wyd2016Bundle\Model\PersonInterface;

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
        return $this->render('Wyd2016Bundle::registration/index.html.twig', array(
            'limitsExceeded' => $this->limitsExceeded(),
        ));
    }

    /**
     * Group form action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function groupFormAction(Request $request)
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new GroupType($translator, $registrationLists);

        $group = new Group();
        $leader = new Pilgrim();
        $leader->setGroup($group);
        $group->setLeader($leader)
            ->addMember($leader);
        $groupMinSize = $this->getParameter('wyd2016.size.min_group');
        $groupMaxSize = $this->getParameter('wyd2016.size.max_group');
        for ($i = 1; $i < $groupMinSize; $i++) {
            $member = new Pilgrim();
            $member->setGroup($group);
            $group->addMember($member);
        }
        $form = $this->createForm($formType, $group, array(
            'action' => $this->generateUrl('registration_group_form'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $members = $group->getMembers();
            if ($members->count() > $groupMaxSize) {
                $group->setMembers(new ArrayCollection($members->slice(0, $groupMaxSize)));
            }
            unset($members);
            $hash = $this->generateActivationHash($leader->getEmail());
            $createdAt = new DateTime();
            $group->setStatus(Group::STATUS_NOT_CONFIRMED)
                ->setActivationHash($hash)
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($createdAt);
            foreach ($group->getMembers() as $i => $member) {
                /** @var Pilgrim $member */
                $isLeader = $member === $group->getLeader();
                // Copies data from form to each pilgrim
                $member->setStatus(Pilgrim::STATUS_NOT_CONFIRMED)
                    ->setActivationHash($this->generateActivationHash($member->getEmail()))
                    ->setCountry($form->get('country')->getData())
                    ->setGroup($group)
                    ->setDatesId($group->getDatesId())
                    ->setCreatedAt($createdAt)
                    ->setUpdatedAt($createdAt);

                /** @var FormInterface $memberView */
                $memberView = $form->get('members')->get($i);
                // Validates age
                $this->validateAge($member, $memberView->get('birthDate'),
                    $isLeader ? 'wyd2016.age.min_adult' : 'wyd2016.age.min_group_member');
            }

            if ($form->isValid()) {
                try {
                    $this->mailSendingProcedure($leader->getEmail(), 'registration_group_confirm',
                        'Wyd2016Bundle::registration/group/email.html.twig', $hash);

                    try {
                        $this->get('wyd2016bundle.group.repository')
                            ->insert($group, true);
                    } catch (Exception $e) {
                        throw new RegistrationException('form.exception.database', 0, $e);
                    }

                    $successMessage = $translator->trans('success.registration.message', array(
                        '%email%' => $leader->getEmail(),
                    ));
                    $this->addMessage($successMessage, 'success');
                    $response = $this->redirect($this->generateUrl('registration_success'));
                } catch (ExceptionInterface $e) {
                    $this->addMessage($e->getMessage(), 'error');
                }
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::registration/group/form.html.twig', array(
                'form' => $form->createView(),
                'max_size' => $groupMaxSize,
                'min_age_member' => $this->getParameter('wyd2016.age.min_group_member'),
                'min_size' => $groupMinSize,
            ));
        }

        return $response;
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
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        $formType = new PilgrimType($this->get('translator'), $this->get('wyd2016bundle.registration.lists'));

        $pilgrim = new Pilgrim();
        $form = $this->createForm($formType, $pilgrim, array(
            'action' => $this->generateUrl('registration_pilgrim_form'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = $this->generateActivationHash($pilgrim->getEmail());
            $createdAt = new DateTime();
            $pilgrim->setStatus(Pilgrim::STATUS_NOT_CONFIRMED)
                ->setActivationHash($hash)
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($createdAt);

            // Validates age
            $this->validateAge($pilgrim, $form->get('birthDate'), 'wyd2016.age.min_adult');

            if ($form->isValid()) {
                try {
                    $this->mailSendingProcedure($pilgrim->getEmail(), 'registration_pilgrim_confirm',
                        'Wyd2016Bundle::registration/pilgrim/email.html.twig', $hash);

                    try {
                        $this->get('wyd2016bundle.pilgrim.repository')
                            ->insert($pilgrim, true);
                    } catch (Exception $e) {
                        throw new RegistrationException('form.exception.database', 0, $e);
                    }

                    $successMessage = $translator->trans('success.registration.message', array(
                        '%email%' => $pilgrim->getEmail(),
                    ));
                    $this->addMessage($successMessage, 'success');
                    $response = $this->redirect($this->generateUrl('registration_success'));
                } catch (ExceptionInterface $e) {
                    $this->addMessage($e->getMessage(), 'error');
                }
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::registration/pilgrim/form.html.twig', array(
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
        if ($this->limitsExceeded()) {
            return $this->render('Wyd2016Bundle::registration/troop/closed.html.twig');
        }

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new TroopType($translator, $registrationLists);

        $troop = new Troop();
        $leader = new Volunteer();
        $leader->setTroop($troop);
        $troop->setLeader($leader)
            ->addMember($leader);
        $troopMinSize = $this->getParameter('wyd2016.size.min_troop');
        $troopMaxSize = $this->getParameter('wyd2016.size.max_troop');
        for ($i = 1; $i < $troopMinSize; $i++) {
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
            $isPolish = $this->isPolish($form);
            if (!$isPolish) {
                // Validates association name existance
                if ($form->get('associationName')->getData() == null) {
                    $form->get('associationName')
                        ->addError(new FormError($translator->trans('form.error.association_name_empty')));
                }
            }

            // Validates services
            if ($form->get('serviceMainId')->getData() == $form->get('serviceExtraId')->getData()) {
                $form->get('serviceExtraId')
                    ->addError(new FormError($translator->trans('form.error.services_duplicated')));
            }

            if ($form->isValid()) {
                $members = $troop->getMembers();
                if ($members->count() > $troopMaxSize) {
                    $troop->setMembers(new ArrayCollection($members->slice(0, $troopMaxSize)));
                }
                unset($members);
                $hash = $this->generateActivationHash($leader->getEmail());
                $createdAt = new DateTime();
                $troop->setStatus(Troop::STATUS_NOT_CONFIRMED)
                    ->setActivationHash($hash)
                    ->setCreatedAt($createdAt)
                    ->setUpdatedAt($createdAt);
                $languages = new ArrayCollection();
                foreach ($form->get('languages')->getData() as $slug) {
                    $language = new Language();
                    $language->setVolunteer($leader)
                        ->setSlug($slug);
                    $languages->add($language);
                }
                $permissions = new ArrayCollection();
                foreach ($form->get('permissions')->getData() as $id) {
                    $permission = new Permission();
                    $permission->setVolunteer($leader)
                        ->setId($id);
                    $permissions->add($permission);
                }
                foreach ($troop->getMembers() as $i => $member) {
                    /** @var Volunteer $member */
                    $isLeader = $member === $troop->getLeader();
                    // Copies data from form to each volunteer
                    $member->setStatus(Volunteer::STATUS_NOT_CONFIRMED)
                        ->setActivationHash($this->generateActivationHash($member->getEmail()))
                        ->setCountry($form->get('country')->getData())
                        ->setTroop($troop)
                        ->setServiceMainId($form->get('serviceMainId')->getData())
                        ->setServiceExtraId($form->get('serviceExtraId')->getData())
                        ->setDatesId($troop->getDatesId())
                        ->setCreatedAt($createdAt)
                        ->setUpdatedAt($createdAt);
                    if ($isLeader && $form->has('otherPermissions')) {
                        $member->setOtherPermissions($form->get('otherPermissions')->getData());
                    }
                    if ($isLeader && $form->has('profession')) {
                        $member->setProfession($form->get('profession')->getData());
                    }
                    // Adds region, district and sex to Polish volunteer or adds association name and removes grade
                    // from foreigner
                    if ($isPolish) {
                        $member->setRegionId($form->get('regionId')->getData())
                            ->setDistrictId($form->get('districtId')->getData())
                            ->setSex($member->getSexFromPesel());
                    } else {
                        $member->setAssociationName($form->get('associationName')->getData())
                            ->setGradeId();
                    }

                    /** @var FormInterface $memberView */
                    $memberView = $form->get('members')->get($i);
                    // Validates age
                    $this->validateAge($member, $memberView->get($isPolish ? 'pesel' : 'birthDate'),
                        $isLeader ? 'wyd2016.age.min_adult' : 'wyd2016.age.min_troop_member');
                    if ($isPolish) {
                        // Validates PESEL existance
                        if ($member->getPesel() == null) {
                            $memberView->get('pesel')
                                ->addError(new FormError($translator->trans('form.error.pesel_empty')));
                        }
                        if ($isLeader) {
                            // Validates leader grade - disabled
//                            if ($member->getGradeId() == $registrationLists::GRADE_NO) {
//                                $memberView->get('gradeId')
//                                    ->addError(new FormError($translator->trans('form.error.grade_inproper')));
//                            }
                            // Validates structure
                            // For leader only to check it only once
                            $this->validateStructure($member, $form->get('districtId'));
                        }
                    }
                }

                if ($form->isValid()) {
                    try {
                        $this->mailSendingProcedure($leader->getEmail(), 'registration_troop_confirm',
                            'Wyd2016Bundle::registration/troop/email.html.twig', $hash, $leader->getSex());

                        try {
                            $this->get('wyd2016bundle.troop.repository')
                                ->insert($troop, true);

                            // Add languages and permissions to leader after save him because of Doctrine requirements
                            $leader->setLanguages($languages)
                                ->setPermissions($permissions);
                            $this->get('wyd2016bundle.volunteer.repository')
                                ->insert($leader, true);
                        } catch (Exception $e) {
                            throw new RegistrationException('form.exception.database', 0, $e);
                        }

                        $successMessage = $translator->trans('success.registration.message', array(
                            '%email%' => $leader->getEmail(),
                        ));
                        $this->addMessage($successMessage, 'success');
                        $response = $this->redirect($this->generateUrl('registration_success'));
                    } catch (ExceptionInterface $e) {
                        $this->addMessage($e->getMessage(), 'error');
                    }
                }
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::registration/troop/form.html.twig', array(
                'form' => $form->createView(),
                'max_size' => $troopMaxSize,
                'min_age_member' => $this->getParameter('wyd2016.age.min_troop_member'),
                'min_size' => $troopMinSize,
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
        if ($this->limitsExceeded()) {
            return $this->render('Wyd2016Bundle::registration/volunteer/closed.html.twig');
        }

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');

        $formType = new VolunteerType($this->get('translator'), $this->get('wyd2016bundle.registration.lists'));

        $volunteer = new Volunteer();
        $form = $this->createForm($formType, $volunteer, array(
            'action' => $this->generateUrl('registration_volunteer_form'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $hash = $this->generateActivationHash($volunteer->getEmail());
            $isPolish = $this->isPolish($form);
            $createdAt = new DateTime();
            $volunteer->setStatus(Volunteer::STATUS_NOT_CONFIRMED)
                ->setActivationHash($hash)
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($createdAt);

            // Adds sex to Polish volunteer or removes grade, region and district from foreigner
            if ($isPolish) {
                $volunteer->setSex($volunteer->getSexFromPesel());
            } else {
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
            } else {
                // Validates association name existance
                if ($volunteer->getAssociationName() == null) {
                    $form->get('associationName')
                        ->addError(new FormError($translator->trans('form.error.association_name_empty')));
                }
            }

            if ($form->isValid()) {
                try {
                    $this->mailSendingProcedure($volunteer->getEmail(), 'registration_volunteer_confirm',
                        'Wyd2016Bundle::registration/volunteer/email.html.twig', $hash, $volunteer->getSex());

                    try {
                        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');

                        // Save volunteer before save its languages and permissions because of Doctrine requirements
                        $languages = $volunteer->getLanguages();
                        foreach ($languages as $language) {
                            /** @var Language $language */
                            $language->setVolunteer($volunteer);
                        }
                        $permissions = $volunteer->getPermissions();
                        foreach ($permissions as $permission) {
                            /** @var Permission $permission */
                            $permission->setVolunteer($volunteer);
                        }
                        $volunteer->setLanguages(new ArrayCollection())
                            ->setPermissions(new ArrayCollection());
                        $volunteerRepository->insert($volunteer, true);
                        $volunteer->setLanguages($languages)
                            ->setPermissions($permissions);

                        $volunteerRepository->insert($volunteer, true);
                    } catch (Exception $e) {
                        throw new RegistrationException('form.exception.database', 0, $e);
                    }

                    $successMessage = $translator->trans('success.registration.message', array(
                        '%email%' => $volunteer->getEmail(),
                    ));
                    $this->addMessage($successMessage, 'success');
                    $response = $this->redirect($this->generateUrl('registration_success'));
                } catch (ExceptionInterface $e) {
                    $this->addMessage($e->getMessage(), 'error');
                }
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::registration/volunteer/form.html.twig', array(
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
        return $this->render('Wyd2016Bundle::registration/success.html.twig', array(
            'limitsExceeded' => $this->limitsExceeded(),
        ));
    }

    /**
     * Group confirm action
     *
     * @param string $hash hash
     *
     * @return Response
     */
    public function groupConfirmAction($hash)
    {
        $response = $this->confirmationProcedureMultiple($this->get('wyd2016bundle.group.repository'),
            $this->get('wyd2016bundle.pilgrim.repository'), $hash, Group::STATUS_CONFIRMED, Pilgrim::STATUS_CONFIRMED);

        return $response;
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
        $response = $this->confirmationProcedureSingle($this->get('wyd2016bundle.pilgrim.repository'), $hash,
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
        $response = $this->confirmationProcedureMultiple($this->get('wyd2016bundle.troop.repository'),
            $this->get('wyd2016bundle.volunteer.repository'), $hash, Troop::STATUS_CONFIRMED,
            Volunteer::STATUS_CONFIRMED);

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
        $response = $this->confirmationProcedureSingle($this->get('wyd2016bundle.volunteer.repository'), $hash,
            Volunteer::STATUS_CONFIRMED);

        return $response;
    }

    /**
     * Mail sending procedure
     *
     * @param string      $email        e-mail
     * @param string      $confirmRoute confirm route
     * @param string      $emailView    email view
     * @param string      $hash         hash
     * @param string|null $sex          sex
     *
     * @throws RegistrationException
     */
    protected function mailSendingProcedure($email, $confirmRoute, $emailView, $hash, $sex = null)
    {
        $translator = $this->get('translator');

        $message = Swift_Message::newInstance()
            ->setSubject($translator->trans('email.title'))
            ->setFrom($this->getParameter('mailer_user'))
            ->setTo($email)
            ->setReplyTo($this->getParameter('wyd2016.email.reply_to'))
            ->setBody($this->renderView($emailView, array(
                'confirmationUrl' => $this->generateUrl($confirmRoute, array(
                        'hash' => $hash,
                    ), UrlGeneratorInterface::ABSOLUTE_URL),
                'sex' => $sex,
            )), 'text/html');

        $mailer = $this->get('mailer');
        if (!$mailer->send($message)) {
            throw new RegistrationException('form.exception.email');
        }
    }

    /**
     * Confirmation procedure single
     *
     * @param BaseRepositoryInterface $repository repository
     * @param string                  $hash       hash
     * @param integer                 $status     status
     *
     * @return Response
     */
    protected function confirmationProcedureSingle(BaseRepositoryInterface $repository, $hash, $status)
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

        return $this->render('Wyd2016Bundle::registration/confirmation.html.twig', array(
            'limitsExceeded' => $this->limitsExceeded(),
        ));
    }

    /**
     * Confirmation procedure multiple
     *
     * @param BaseRepositoryInterface $multipleRepository multiple repository
     * @param BaseRepositoryInterface $singleRepository   single repository
     * @param string                  $hash               hash
     * @param integer                 $multipleStatus     multiple status
     * @param integer                 $singleStatus       single status
     *
     * @return Response
     */
    protected function confirmationProcedureMultiple(BaseRepositoryInterface $multipleRepository,
        BaseRepositoryInterface $singleRepository, $hash, $multipleStatus, $singleStatus)
    {
        /** @var Group|Troop $entity */
        $entity = $multipleRepository->findOneBy(array(
            'activationHash' => $hash,
        ));

        if (!isset($entity) || $entity->isConfirmed()) {
            $this->addMessage('confirmation.error', 'error');
        } else {
            foreach ($entity->getMembers() as $member) {
                /** @var Pilgrim|Volunteer $member */
                if (!$member->isConfirmed()) {
                    $member->setStatus($singleStatus);
                    $singleRepository->update($member);
                }
            }
            $singleRepository->flush();
            $entity->setStatus($multipleStatus);
            $multipleRepository->update($entity, true);
            $this->addMessage('confirmation.success', 'success');
        }

        return $this->render('Wyd2016Bundle::registration/confirmation.html.twig', array(
            'limitsExceeded' => $this->limitsExceeded(),
        ));
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
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');

        $ageMin = $this->getParameter($minAgeParamName);
        $ageMax = $this->getParameter('wyd2016.age.max');
        $ageLimit = new DateTime($this->getParameter('wyd2016.age.limit'));

        $birthDate = $person->getBirthDate();
        if (!isset($birthDate)) {
            $ageField->addError(new FormError($translator->trans('form.error.birth_date_not_specified')));
        } else {
            $age = (integer) $birthDate->diff($ageLimit->modify('-1 day'))
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
    }

    /**
     * Validate structure
     *
     * @param Volunteer     $volunteer     volunteer
     * @param FormInterface $districtField district field
     */
    protected function validateStructure(Volunteer $volunteer, FormInterface $districtField)
    {
        /** @var TranslatorInterface $translator */
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
     * Add error message
     *
     * @param FormInterface $form
     */
    protected function addErrorMessage(FormInterface $form)
    {
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addMessage('form.errors', 'error');
        }
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

    /**
     * Limits exceeded
     *
     * @return boolean
     */
    protected function limitsExceeded()
    {
        $timeLimit = new DateTime($this->getParameter('wyd2016.time.limit'));
        if (new DateTime('now') > $timeLimit) {
            return true;
        }

        $numberTotalLimit = $this->getParameter('wyd2016.number.total_limit');
        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');
        if ($volunteerRepository->getTotalNumber() >= $numberTotalLimit) {
            return true;
        }

        return false;
    }
}
