<?php

namespace Wyd2016Bundle\Controller;

use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Model\Language;
use Wyd2016Bundle\Model\User;
use Wyd2016Bundle\Model\Volunteer;
use Wyd2016Bundle\Twig\WydExtension;

/**
 * Controller
 */
class ApiController extends Controller
{
    /** @var User */
    protected $user;

    /**
     * Volunteer action
     *
     * @param Request $request request
     * @param int     $id      ID
     *
     * @return JsonResponse
     */
    public function volunteerAction(Request $request, $id)
    {
        if (!$this->hasAccess($request)) {
            return $this->getJsonResponse(Response::HTTP_UNAUTHORIZED);
        }

        if ($id <= 0) {
            return $this->getJsonResponse(Response::HTTP_NOT_FOUND);
        }
        /** @var Volunteer $volunteer */
        $volunteer = $this->get('wyd2016bundle.volunteer.repository')
            ->findOneBy([
                'id' => $id,
            ]);
        if (!isset($volunteer)) {
            return $this->getJsonResponse(Response::HTTP_NOT_FOUND);
        }

        /** @var WydExtension $filters */
        $filters = $this->get('wyd2016bundle.twig_extension.wyd');

        $volunteerData = array(
            'id' => $volunteer->getId(),
            'status' => $volunteer->getStatus(),
            'firstName' => $volunteer->getFirstName(),
            'lastName' => $volunteer->getLastName(),
            'birthDate' => $volunteer->getBirthDate() != null ? $volunteer->getBirthDate()
                ->format('d/m/Y') : null,
            'sex' => $volunteer->getSex(),
            'country' => $volunteer->getCountry(),
            'address' => $volunteer->getAddress(),
            'phone' => $volunteer->getPhone(),
            'email' => $volunteer->getEmail(),
            'emailAlias' => str_replace('{id}', $volunteer->getId(), $this->getEmailAlias('volunteer')),
            'shirtSize' => $filters->shirtSizeNameFilter($volunteer->getShirtSize()),
            'region' => $filters->regionNameFilter($volunteer->getRegionId()),
            'district' => $filters->districtNameFilter($volunteer->getDistrictId()),
            'pesel' => $filters->peselModifyFilter($volunteer->getPesel(), true),
            'languages' => $this->getLanguages($volunteer->getLanguages()),
        );

        return $this->getJsonResponse(Response::HTTP_OK, array(
            'volunteer' => $volunteerData,
        ));
    }

    /**
     * Get languages
     *
     * @param Collection $languages languages
     *
     * @return array
     */
    protected function getLanguages(Collection $languages)
    {
        $list = array();
        /** @var Language $language */
        foreach ($languages as $language) {
            $list[] = $language->getSlug();
        }

        return $list;
    }

    /**
     * Has access
     *
     * @param Request $request request
     *
     * @return boolean
     */
    protected function hasAccess(Request $request)
    {
        $userName = $request->query->get('user');
        $token = $request->query->get('token');

        if (empty($userName) || empty($token) || !preg_match('#^[0-9a-f]{32}$#i', $token)) {
            return false;
        }
        $this->user = $this->get('wyd2016bundle.user.repository')
            ->findOneBy([
                'username' => $userName,
            ]);
        if (!isset($this->user) || $this->user->getApiToken() != $token) {
            return false;
        }

        return true;
    }

    /**
     * Get e-mail alias
     *
     * @param string $type type
     *
     * @return string
     */
    protected function getEmailAlias($type)
    {
        $emailAliases = $this->getParameter('wyd2016.email_alias');
        $emailAlias = array_key_exists($type, $emailAliases) ? $emailAliases[$type] : null;

        return $emailAlias;
    }

    /**
     * Get JSON response
     *
     * @param int   $status  status
     * @param array $data    data
     * @param array $headers headers
     *
     * @return JsonResponse
     */
    protected function getJsonResponse($status, array $data = array(), array $headers = array())
    {
        $data['status'] = $status == Response::HTTP_OK ? 'success' : 'error';
        $mergedHeaders = array_merge(array(
            'Access-Control-Allow-Methods' => 'GET',
            'Access-Control-Allow-Origin' => '*',
        ), $headers);

        return new JsonResponse($data, $status, $mergedHeaders);
    }
}
