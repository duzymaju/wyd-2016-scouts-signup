<?php

namespace Wyd2016Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Model\User;
use Wyd2016Bundle\Model\Volunteer;

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

        $volunteerData = array(
            'firstName' => $volunteer->getFirstName(),
            'lastName' => $volunteer->getLastName(),
        );

        return $this->getJsonResponse(Response::HTTP_OK, array(
            'volunteer' => $volunteerData,
        ));
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
        $userName = $request->request->get('user');
        $token = $request->request->get('token');

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

        return new JsonResponse($data, $status, $headers);
    }
}
