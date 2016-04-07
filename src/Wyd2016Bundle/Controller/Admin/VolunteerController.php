<?php

namespace Wyd2016Bundle\Controller\Admin;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Model\Language;
use Wyd2016Bundle\Model\Permission;
use Wyd2016Bundle\Twig\WydExtension;

/**
 * Admin controller
 */
class VolunteerController extends AbstractController
{
    /**
     * Index action
     *
     * @param integer $pageNo page no
     *
     * @return Response
     */
    public function indexAction($pageNo)
    {
        /** @var Paginator $volunteers */
        $volunteers = $this->getRepository()
            ->getPackOrException($pageNo, $this->getParameter('wyd2016.admin.pack_size'), array(), array(
                'createdAt' => 'DESC',
            ));

        return $this->render('Wyd2016Bundle::admin/volunteer/index.html.twig', array(
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
     * List action
     *
     * @return Response
     */
    public function listAction()
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var WydExtension $filters */
        $filters = $this->get('wyd2016bundle.twig_extension.wyd');
        $volunteers = $this->getRepository()
            ->getAllOrderedBy(array(
                'createdAt' => 'DESC',
            ));

        $data = array();
        $data[] = array(
            $translator->trans('form.status'),
            $translator->trans('form.first_name'),
            $translator->trans('form.last_name'),
            $translator->trans('form.address'),
            $translator->trans('form.phone'),
            $translator->trans('form.email'),
            $translator->trans('form.country'),
            $translator->trans('form.birth_date'),
            $translator->trans('admin.age_at_limit', [
                '%date%' => $this->getParameter('wyd2016.age.limit'),
            ]),
            $translator->trans('form.sex'),
            $translator->trans('form.grade'),
            $translator->trans('form.region'),
            $translator->trans('form.district'),
            $translator->trans('form.pesel'),
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
                $filters->statusNameFilter($volunteer->getStatus()),
                $volunteer->getFirstName(),
                $volunteer->getLastName(),
                $volunteer->getAddress(),
                $volunteer->getPhone(),
                $volunteer->getEmail(),
                $filters->localizedCountryFilter($volunteer->getCountry()),
                $volunteer->getBirthDate()
                    ->format('Y-m-d'),
                $filters->ageAtLimitFilter($volunteer->getBirthDate()),
                $filters->sexNameFilter($volunteer->getSex()),
                $volunteer->getGradeId() > 0 ? $filters->gradeNameFilter($volunteer->getGradeId()) : '-',
                $volunteer->getRegionId() > 0 ? $filters->regionNameFilter($volunteer->getRegionId()) : '-',
                $volunteer->getDistrictId() > 0 ? $filters->districtNameFilter($volunteer->getDistrictId()) : '-',
                $volunteer->getPesel() > 0 ? $filters->peselModifyFilter($volunteer->getPesel()) : '-',
                $volunteer->getShirtSize() > 0 ? $filters->shirtSizeNameFilter($volunteer->getShirtSize()) : '-',
                $filters->serviceNameFilter($volunteer->getServiceMainId()),
                $volunteer->getServiceExtraId() ? $filters->serviceNameFilter($volunteer->getServiceExtraId()) : '-',
                $this->getLanguagesList($volunteer->getLanguages(), $filters),
                $this->getPermissionsList($volunteer->getPermissions(), $filters),
                $volunteer->getOtherPermissions(),
                $volunteer->getProfession(),
                $filters->pilgrimDateFilter($volunteer->getDatesId()),
                empty($volunteer->getComments()) ? '-' : $volunteer->getComments(),
                $volunteer->getTroop() ? $volunteer->getTroop()
                    ->getName() : '-',
                $volunteer->getCreatedAt()
                    ->format('Y-m-d'),
            );
        }

        return $this->getCsvResponse($data, 'volunteer_list');
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
