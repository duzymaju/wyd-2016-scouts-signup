<?php

namespace Wyd2016Bundle\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\VolunteerSupplementType;
use Wyd2016Bundle\Model\Action;

/**
 * Controller
 */
class ParticipantController extends Controller
{
    /**
     * Index action
     *
     * @param Request $request request
     * @param string  $hash    hash
     *
     * @return Response
     */
    public function volunteerSupplementAction(Request $request, $hash)
    {
        /** @var VolunteerRepository $volunteerRepository */
        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');
        /** @var Volunteer $volunteer */
        $volunteer = $volunteerRepository->findOneByOrException(array(
            'activationHash' => $hash,
        ));

        $supplement = $this->get('wyd2016bundle.manager.supplement')
            ->getVolunteerSupplement($volunteer);
        if (!$supplement->ifAskForAnything()) {
            return $this->render('Wyd2016Bundle::participant/volunteer/complete.html.twig', array(
                'volunteer' => $volunteer,
            ));
        }

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new VolunteerSupplementType($translator, $registrationLists, $volunteer, $supplement);

        $form = $this->createForm($formType, $volunteer, array(
            'action' => $this->generateUrl('participant_volunteer_supplement', array(
                'hash' => $hash,
            )),
            'method' => 'POST',
            'validation_groups' => 'supplement',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Validates services
            if ($volunteer->getServiceMainId() == $volunteer->getServiceExtraId()) {
                $form->get('serviceExtraId')
                    ->addError(new FormError($translator->trans('form.error.services_duplicated')));
            }
            if ($form->isValid()) {
                try {
                    $now = new DateTime();
                    $volunteer->setUpdatedAt($now);
                    $volunteerRepository->update($volunteer, true);
                    $actionManager = $this->get('wyd2016bundle.manager.action');
                    if (($supplement->ifAskForDistrict() || $supplement->ifAskForService() ||
                        $supplement->ifAskForDates()) && $volunteer->isTroopLeader())
                    {
                        $troop = $volunteer->getTroop();
                        if ($supplement->ifAskForDates()) {
                            $troop->setDatesId($form->get('datesId')->getData());
                            $this->get('wyd2016bundle.troop.repository')
                                ->update($troop, true);
                        }
                        /** @var Volunteer $member */
                        foreach ($troop->getMembers() as $member) {
                            if ($member == $volunteer) {
                                continue;
                            }
                            if ($supplement->ifAskForDistrict()) {
                                $member->setDistrictId($form->get('districtId')->getData());
                            }
                            if ($supplement->ifAskForService()) {
                                $member->setServiceMainId($form->get('serviceMainId')->getData())
                                    ->setServiceExtraId($form->get('serviceExtraId')->getData());
                            }
                            if ($supplement->ifAskForDates()) {
                                $member->setDatesId($form->get('datesId')->getData());
                            }
                            $member->setUpdatedAt($now);
                            $volunteerRepository->update($member);
                        }
                        $volunteerRepository->flush();
                        $actionManager->log(Action::TYPE_UPDATE_TROOP_DATA, $troop->getId());
                    }
                    $actionManager->log(Action::TYPE_UPDATE_VOLUNTEER_DATA, $volunteer->getId());
                    $this->addMessage($translator->trans('success.supplement.message'), 'success');
                    $response = $this->redirect($this->generateUrl('registration_success'));
                } catch (ExceptionInterface $e) {
                    unset($e);
                    $this->addMessage('form.exception.database', 'error');
                }
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::participant/volunteer/supplement.html.twig', array(
                'form' => $form->createView(),
                'volunteer' => $volunteer,
            ));
        }

        return $response;
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
}
