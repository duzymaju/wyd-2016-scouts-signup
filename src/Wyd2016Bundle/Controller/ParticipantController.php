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

        $askForDistrict = $this->ifAskForDistrict($volunteer);
        $askForFatherName = $this->ifAskForFatherName($volunteer);
        $askForService = $this->ifAskForService($volunteer);

        if (!$askForDistrict && !$askForFatherName && !$askForService) {
            return $this->render('Wyd2016Bundle::participant/volunteer/complete.html.twig', array(
                'volunteer' => $volunteer,
            ));
        }

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new VolunteerSupplementType($translator, $registrationLists, $volunteer, array(
            'district' => $askForDistrict,
            'fatherName' => $askForFatherName,
            'service' => $askForService,
        ));

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
                    if ($askForDistrict || $askForService) {
                        $troop = $volunteer->getTroop();
                        /** @var Volunteer $member */
                        foreach ($troop->getMembers() as $member) {
                            if ($member == $volunteer) {
                                continue;
                            }
                            if ($askForDistrict) {
                                $member->setDistrictId($form->get('districtId')->getData());
                            }
                            if ($askForService) {
                                $member->setServiceMainId($form->get('serviceMainId')->getData())
                                    ->setServiceExtraId($form->get('serviceExtraId')->getData());
                            }
                            $member->setUpdatedAt($now);
                            $volunteerRepository->update($member);
                        }
                        $volunteerRepository->flush();
                    }
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
     * If ask for district
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForDistrict(Volunteer $volunteer)
    {
        $districtId = $volunteer->getDistrictId();
        if (empty($districtId)) {
            return false;
        }

        if ($volunteer->getUpdatedAt() >= new DateTime('2016-06-03 02:00:00')) {
            return false;
        }

        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $districts = $registrationLists->getDistricts($volunteer->getRegionId());
        $districtIds = array_keys($districts);
        if (array_shift($districtIds) != $districtId) {
            return false;
        }

        if (!$this->isTroopLeader($volunteer)) {
            return false;
        }

        return true;
    }

    /**
     * If ask for father name
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForFatherName(Volunteer $volunteer)
    {
        $fatherName = $volunteer->getFatherName();
        if (!empty($fatherName)) {
            return false;
        }

        return true;
    }

    /**
     * If ask for service
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function ifAskForService(Volunteer $volunteer)
    {
        if ($volunteer->getServiceMainId() != RegistrationLists::SERVICE_UNDERAGE) {
            return false;
        }

        if (!$this->isTroopLeader($volunteer)) {
            return false;
        }

        return true;
    }

    /**
     * Is troop leader
     *
     * @param Volunteer $volunteer volunteer
     *
     * @return boolean
     */
    protected function isTroopLeader(Volunteer $volunteer)
    {
        $troop = $volunteer->getTroop();
        $isTroopLeader = isset($troop) && $troop->getLeader() == $volunteer;

        return $isTroopLeader;
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
