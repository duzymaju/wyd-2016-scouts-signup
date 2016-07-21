<?php

namespace Wyd2016Bundle\Controller\Admin;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Exception\ExceptionInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\VolunteerEditType;
use Wyd2016Bundle\Model\Action;
use Wyd2016Bundle\Model\Language;
use Wyd2016Bundle\Model\Permission;
use Wyd2016Bundle\Twig\WydExtension;

/**
 * Admin controller
 */
class VolunteerController extends AbstractController
{
    /** @var integer[] */
    protected $adultsIndividual = [2, 7, 8, 13, 14, 15, 16, 17, 18, 20, 21, 22, 30, 31, 33, 35, 36, 37, 38, 40, 41, 42, 44, 46, 47, 48, 49, 52, 53, 55, 56, 57, 58, 60, 61, 62, 63, 69, 72, 77, 78, 81, 82, 83, 85, 87, 88, 89, 90, 91, 92, 93, 97, 102, 103, 107, 108, 111, 112, 113, 114, 115, 118, 120, 122, 123, 124, 127, 129, 130, 131, 133, 136, 139, 140, 141, 143, 149, 151, 152, 155, 156, 157, 159, 160, 164, 169, 170, 171, 172, 173, 174, 175, 177, 179, 180, 181, 182, 183, 184, 186, 187, 188, 189, 190, 191, 192, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 210, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 237, 238, 240, 241, 242, 243, 247, 248, 250, 251, 252, 253, 256, 259, 265, 266, 267, 268, 269, 272, 276, 277, 278, 280, 284, 285, 286, 289, 290, 291, 293, 294, 295, 296, 300, 302, 303, 305, 306, 310, 313, 315, 321, 322, 326, 327, 328, 329, 330, 333, 334, 335, 336, 337, 338, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 359, 361, 362, 363, 364, 365, 366, 371, 372, 373, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 401, 402, 403, 404, 405, 406, 409, 410, 412, 413, 424, 426, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 461, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 479, 482, 488, 489, 490, 491, 493, 498, 499, 506, 507, 511, 520, 526, 527, 532, 533, 537, 538, 540, 541, 542, 543, 544, 545, 547, 550, 552, 562, 563, 564, 565, 566, 567, 568, 569, 570, 575, 576, 577, 578, 579, 580, 581, 584, 585, 586, 587, 590, 593, 594, 596, 597, 601, 602, 603, 606, 607, 608, 611, 613, 616, 622, 627, 631, 632, 633, 634, 635, 645, 647, 651, 654, 658, 665, 666, 667, 684, 689, 691, 692, 694, 695, 696, 697, 698, 699, 700, 701, 702, 703, 704, 717, 718, 735, 737, 738, 744, 745, 746, 748, 752, 753, 755, 757, 759, 760, 763, 764, 765, 766, 767, 768, 777, 781, 785, 788, 790, 793, 795, 797, 798, 806, 807, 808, 809, 810, 811, 816, 822, 823, 824, 825, 826, 828];

    /**
     * Index action
     *
     * @param Request $request request
     * @param integer $pageNo  page no
     *
     * @return Response
     */
    public function indexAction(Request $request, $pageNo)
    {
        $criteriaSettings = array(
            'regionId' => 'getRegion',
            'districtId' => 'getDistrict',
            'serviceMainId' => array(
                'getter' => 'getService',
                'queryId' => 'serviceId',
            ),
            'status' => array(
                'getter' => 'getStatus',
                'lowestValue' => 0,
            ),
        );
        $criteria = $this->getCriteria($request->query, $criteriaSettings);

        $orderBy = array(
            'createdAt' => 'DESC',
        );
        if (array_key_exists('regionId', $criteria)) {
            $orderBy = array_merge(array(
                'districtId' => 'ASC',
            ), $orderBy);
        }

        /** @var Paginator $volunteers */
        $volunteers = $this->getRepository()
            ->getPackOrException($pageNo, $this->getParameter('wyd2016.admin.pack_size'), $criteria, $orderBy, array(
                't' => 'troop',
            ));

        return $this->render('Wyd2016Bundle::admin/volunteer/index.html.twig', array(
            'criteria' => $criteria,
            'volunteers' => $volunteers->setRouteName('admin_volunteer_index'),
        ));
    }

    /**
     * Show action
     *
     * @param Request $request request
     * @param integer $id      ID
     *
     * @return Response
     */
    public function showAction(Request $request, $id)
    {
        /** @var Volunteer $volunteer */
        $volunteer = $this->getRepository()
            ->findOneByOrException(array(
                'id' => $id,
            ));

        $response = $this->sendReminderIfRequested($volunteer, $request, $this->getRepository(),
            'registration_volunteer_confirm', 'Wyd2016Bundle::admin/volunteer/email.html.twig');

        if (!isset($response)) {
            $response = $this->render('Wyd2016Bundle::admin/volunteer/show.html.twig', array(
                'ageLimit' => $this->getParameter('wyd2016.age.limit'),
                'isReminderSendingPossible' => $this->isReminderSendingPossible($volunteer) && !$volunteer->getTroop(),
                'volunteer' => $volunteer,
            ));
        }

        return $response;
    }

    /**
     * Edit action
     *
     * @param Request $request request
     * @param integer $id      ID
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var VolunteerRepository $volunteerRepository */
        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');
        /** @var Volunteer $volunteer */
        $volunteer = $volunteerRepository->findOneByOrException(array(
            'id' => $id,
        ));

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new VolunteerEditType($translator, $registrationLists);

        $form = $this->createForm($formType, $volunteer, array(
            'action' => $this->generateUrl('admin_volunteer_edit', array(
                'id' => $id,
            )),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $volunteer->setUpdatedAt(new DateTime());
                $volunteerRepository->update($volunteer, true);

                $this->get('wyd2016bundle.manager.action')
                    ->log(Action::TYPE_UPDATE_VOLUNTEER_DATA, $volunteer->getId(), $this->getUser());

                $this->addMessage('admin.edit.success', 'success');
                $response = $this->softRedirect($this->generateUrl('admin_volunteer_show', array(
                    'id' => $id,
                )));
            } catch (ExceptionInterface $e) {
                unset($e);
                $this->addMessage('form.exception.database', 'error');
            }
        }
        if (!isset($response)) {
            $this->addErrorMessage($form);
            $response = $this->render('Wyd2016Bundle::admin/volunteer/edit.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        return $response;
    }

    /**
     * List action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var WydExtension $filters */
        $filters = $this->get('wyd2016bundle.twig_extension.wyd');
        $volunteersRepository = $this->getRepository();

        $type = $request->query->get('type');
        $wydAdult = $this->getParameter('wyd2016.wyd_adult');
        $orderBy = array(
            'createdAt' => 'DESC',
        );
        switch ($type) {
            case 'adultsIndividual':
            case 'adults':
                $volunteers = $volunteersRepository->getFullInfoBy(array(
                    'id' => $this->adultsIndividual,
                    'birthDate.lt' => $wydAdult,
                ), $orderBy);
                $type = 'adultsIndividual';
                break;

            case 'adultsAsGroup':
                $volunteers = $volunteersRepository->getFullInfoBy(array(
                    'id.not' => $this->adultsIndividual,
                    'birthDate.lt' => $wydAdult,
                ), $orderBy);
                break;

            case 'childrenAsGroup':
            case 'children':
                $volunteers = $volunteersRepository->getFullInfoBy(array(
                    'birthDate.gte' => $wydAdult,
                ), $orderBy);
                $type = 'children';
                break;

            case 'all':
            default:
                $volunteers = $volunteersRepository->getAllOrderedBy($orderBy);
                $type = null;
        }

        $showPesel = (boolean) $request->query->get('showPesel');

        $data = array();
        $data[] = array(
            $translator->trans('form.id'),
            $translator->trans('form.status'),
            $translator->trans('form.first_name'),
            $translator->trans('form.last_name'),
            $translator->trans('form.address'),
            $translator->trans('form.phone'),
            $translator->trans('form.email'),
            $translator->trans('form.country'),
            $translator->trans('form.association_name'),
            $translator->trans('form.birth_date'),
            $translator->trans('admin.age_at_limit', [
                '%date%' => $this->getParameter('wyd2016.age.limit'),
            ]),
            $translator->trans('form.sex'),
            $translator->trans('form.grade'),
            $translator->trans('form.region'),
            $translator->trans('form.district'),
            $translator->trans('form.pesel'),
            $translator->trans('form.father_name'),
            $translator->trans('form.shirt_size'),
            $translator->trans('form.service_main'),
            $translator->trans('form.service_extra'),
            $translator->trans('form.languages'),
            $translator->trans('form.permissions'),
            $translator->trans('form.other_permissions'),
            $translator->trans('form.profession'),
            $translator->trans('form.dates'),
            $translator->trans('form.comments'),
            $translator->trans('form.troop_name'),
            $translator->trans('admin.created_at'),
        );
        foreach ($volunteers as $volunteer) {
            /** @var Volunteer $volunteer */
            $data[] = array(
                $volunteer->getId(),
                $filters->statusNameFilter($volunteer->getStatus()),
                $volunteer->getFirstName(),
                $volunteer->getLastName(),
                $volunteer->getAddress(),
                $volunteer->getPhone(),
                $volunteer->getEmail(),
                $filters->localizedCountryFilter($volunteer->getCountry()),
                $volunteer->getAssociationName(),
                $volunteer->getBirthDate()
                    ->format('Y-m-d'),
                $filters->ageAtLimitFilter($volunteer->getBirthDate()),
                $filters->sexNameFilter($volunteer->getSex()),
                $volunteer->getGradeId() > 0 ? $filters->gradeNameFilter($volunteer->getGradeId()) : '-',
                $volunteer->getRegionId() > 0 ? $filters->regionNameFilter($volunteer->getRegionId()) : '-',
                $volunteer->getDistrictId() > 0 ? $filters->districtNameFilter($volunteer->getDistrictId()) : '-',
                $volunteer->getPesel() > 0 ? $filters->peselModifyFilter($volunteer->getPesel(), $showPesel) : '-',
                $volunteer->getFatherName() ? $volunteer->getFatherName() : '-',
                $volunteer->getShirtSize() > 0 ? $filters->shirtSizeNameFilter($volunteer->getShirtSize()) : '-',
                $filters->serviceNameFilter($volunteer->getServiceMainId()),
                $volunteer->getServiceExtraId() ? $filters->serviceNameFilter($volunteer->getServiceExtraId()) : '-',
                $this->getLanguagesList($volunteer->getLanguages(), $filters),
                $this->getPermissionsList($volunteer->getPermissions(), $filters),
                $volunteer->getOtherPermissions(),
                $volunteer->getProfession(),
                $filters->volunteerDateFilter($volunteer->getDatesId()),
                empty($volunteer->getComments()) ? '-' : $volunteer->getComments(),
                $volunteer->getTroop() ? $volunteer->getTroop()
                    ->getName() : '-',
                $volunteer->getCreatedAt()
                    ->format('Y-m-d'),
            );
        }

        return $this->getCsvResponse($data, 'volunteer_list' . (empty($type) ? '' : '_' . $type));
    }

    /**
     * Get languages list
     *
     * @param Collection   $languages languages
     * @param WydExtension $filters   filters
     *
     * @return string
     */
    protected function getLanguagesList(Collection $languages, WydExtension $filters)
    {
        $list = array();
        if ($languages->count() > 0) {
            foreach ($languages as $language) {
                /** @var Language $language */
                $languageName = $filters->languageNameFilter($language->getSlug());
                if (!empty($languageName)) {
                    $list[] = $languageName;
                }
            }
        }
        if (count($list) < 1) {
            $list[] = '-';
        }

        return implode(', ', $list);
    }

    /**
     * Get permissions list
     *
     * @param Collection   $permissions permissions
     * @param WydExtension $filters     filters
     *
     * @return string
     */
    protected function getPermissionsList(Collection $permissions, WydExtension $filters)
    {
        $list = array();
        if ($permissions->count() > 0) {
            foreach ($permissions as $permission) {
                /** @var Permission $permission */
                $permissionName = $filters->permissionNameFilter($permission->getId());
                if (!empty($permissionName)) {
                    $list[] = $permissionName;
                }
            }
        }
        if (count($list) < 1) {
            $list[] = '-';
        }

        return implode(', ', $list);
    }

    /**
     * Get repository
     *
     * @return VolunteerRepository
     */
    protected function getRepository()
    {
        $repository = $this->get('wyd2016bundle.volunteer.repository');

        return $repository;
    }
}
