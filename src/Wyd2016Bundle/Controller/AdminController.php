<?php

namespace Wyd2016Bundle\Controller;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller
 */
class AdminController extends Controller
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction()
    {
        $criteria = array();
        $orderBy = array(
            'createdAt' => 'DESC',
        );
        $limit = 5;

        $volunteerRepository = $this->get('wyd2016bundle.volunteer.repository');
        $troopRepository = $this->get('wyd2016bundle.troop.repository');
        $pilgrimRepository = $this->get('wyd2016bundle.pilgrim.repository');
        $groupRepository = $this->get('wyd2016bundle.group.repository');

        return $this->render('Wyd2016Bundle::admin/index.html.twig', array(
            'limit' => $limit,
            'lists' => array(
                'volunteers' => array(
                    'counter' => 'admin.volunteers.counter',
                    'items' => $volunteerRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_volunteer_index',
                    'routeShow' => 'admin_volunteer_show',
                    'title' => 'admin.volunteers',
                    'totalNumber' => $volunteerRepository->getTotalNumber(),
                ),
                'troops' => array(
                    'counter' => 'admin.troops.counter',
                    'items' => $troopRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_troop_index',
                    'routeShow' => 'admin_troop_show',
                    'title' => 'admin.troops',
                    'totalNumber' => $troopRepository->getTotalNumber(),
                ),
                'pilgrims' => array(
                    'counter' => 'admin.pilgrims.counter',
                    'items' => $pilgrimRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_pilgrim_index',
                    'routeShow' => 'admin_pilgrim_show',
                    'title' => 'admin.pilgrims',
                    'totalNumber' => $pilgrimRepository->getTotalNumber(),
                ),
                'groups' => array(
                    'counter' => 'admin.groups.counter',
                    'items' => $groupRepository->findBy($criteria, $orderBy, $limit),
                    'routeIndex' => 'admin_group_index',
                    'routeShow' => 'admin_group_show',
                    'title' => 'admin.groups',
                    'totalNumber' => $groupRepository->getTotalNumber(),
                ),
            ),
            'stats' => array(
                'countries' => $volunteerRepository->countByCountries(),
                'regions' => $volunteerRepository->countByRegions(),
                'services' => $volunteerRepository->countByServices(),
            ),
        ));
    }

    /**
     * Notify regions action
     *
     * @param Request $request request
     * @param string  $period  period
     *
     * @return Response
     */
    public function notifyRegionsAction(Request $request, $period)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $command = array(
           'command' => 'regions:notify',
           '--period' => $period,
        );

        $receiver = $request->query->get('receiver');
        if (!empty($receiver)) {
            $command['--receiver'] = $receiver;
        }

        $input = new ArrayInput($command);
        $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        $application->run($input, $output);

        $converter = new AnsiToHtmlConverter();
        $content = $output->fetch();

        return new Response($converter->convert($content));
    }

    /**
     * Main action
     *
     * @return Response
     */
    public function mainAction()
    {
        return $this->redirect($this->generateUrl('admin_index'));
    }
}
