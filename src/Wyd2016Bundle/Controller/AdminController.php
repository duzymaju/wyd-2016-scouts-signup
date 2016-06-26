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
use Wyd2016Bundle\Command\SupplementLinkSendCommand;

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

        $languageRepository = $this->get('wyd2016bundle.language.repository');
        $permissionRepository = $this->get('wyd2016bundle.permission.repository');

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
                'languages' => $languageRepository->countByTypes(),
                'permissions' => $permissionRepository->countByTypes(),
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
        $options = array(
           'period' => $period,
        );

        $receiver = $request->query->get('receiver');
        if (!empty($receiver)) {
            $options['receiver'] = $receiver;
        }

        return $this->executeCommand('regions:notify', array(), $options);
    }

    /**
     * Format data action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function formatDataAction(Request $request)
    {
        $options = array(
            'countryCodeAdd' => $request->query->getBoolean('countryCodeAdd', false),
            'update' => $request->query->getBoolean('update', false),
        );

        return $this->executeCommand('data:format', array(), $options);
    }

    /**
     * Supplement link send action
     *
     * @param Request $request request
     * @param string  $set     set
     *
     * @return Response
     */
    public function supplementLinkSendAction(Request $request, $set)
    {
        $name = 'supplement:link:send';
        $page = $request->query->getInt('page');
        if ($page < 1) {
            return $this->render('Wyd2016Bundle::admin/command_loop.html.twig', array(
                'name' => $name,
            ));
        }

        $arguments = array(
            'type' => SupplementLinkSendCommand::TYPE_VOLUNTEER,
            'set' => $set,
        );
        $options = array(
            'pack' => $set == SupplementLinkSendCommand::SET_TROOPS ? 10 : 20,
            'page' => $page,
            'test' => $request->query->getBoolean('test', false),
            'supplement' => $request->query->getBoolean('supplement', false),
            'wydform' => $request->query->getBoolean('wydform', false),
        );

        $ids = $request->query->get('ids');
        if (!empty($ids)) {
            $options['ids'] = $ids;
        }
        $receiver = $request->query->get('receiver');
        if (!empty($receiver)) {
            $options['receiver'] = $receiver;
        }

        return $this->executeCommand($name, $arguments, $options);
    }

    /**
     * Execute command
     * 
     * @param string  $name      name
     * @param array   $arguments arguments
     * @param array   $options   options
     * @param boolean $wholeView whole view
     * 
     * @return Response
     */
    protected function executeCommand($name, array $arguments = array(), array $options = array(), $wholeView = true)
    {
        $command = array(
            'command' => $name,
        );
        foreach ($arguments as $argumentName => $argumentValue) {
            $argumentName = ltrim($argumentName, '-');
            if (!array_key_exists($argumentName, $command)) {
                $command[$argumentName] = $argumentValue;
            }
        }
        foreach ($options as $optionName => $optionValue) {
            $optionName = ltrim($optionName, '-');
            if (!array_key_exists($optionName, $command)) {
                $id = strlen($optionName) == 1 ? '-' . $optionName : '--' . $optionName;
                $command[$id] = $optionValue;
            }
        }

        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput($command);
        $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        $application->run($input, $output);

        $converter = new AnsiToHtmlConverter();
        $content = $output->fetch();

        if ($wholeView) {
            return $this->render('Wyd2016Bundle::admin/command.html.twig', array(
                'content' => $converter->convert($content),
                'name' => $name,
            ));
        } else {
            return new Response($converter->convert($content));
        }
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
