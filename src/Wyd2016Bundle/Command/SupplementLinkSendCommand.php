<?php

namespace Wyd2016Bundle\Command;

use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wyd2016Bundle\Model\Troop;
use Wyd2016Bundle\Model\Volunteer;

/**
 * Command
 */
class SupplementLinkSendCommand extends ContainerAwareCommand
{
    /** @var string */
    const TYPE_VOLUNTEER = 'volunteer';

    /** @var string */
    const SET_VOLUNTEERS = 'volunteers';

    /** @var string */
    const SET_TROOPS = 'troops';

    /** @var string */
    protected $emailAlias;

    /** @var array */
    protected $loginPage;

    /** @var boolean */
    protected $isTest;

    /** @var string|null */
    protected $receiver;

    /** @var integer */
    protected $limit;

    /** @var integer */
    protected $offset;

    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('supplement:link:send')
            ->setDescription('Notify region headquarters about new volunteers from their regions.')
            ->addArgument('type', InputArgument::REQUIRED, 'Type of supplement.')
            ->addArgument('set', InputArgument::REQUIRED, 'Set of elements to work with.')
            ->addOption('test', 't', InputOption::VALUE_REQUIRED, 'Testing (without sending e-mails).', false)
            ->addOption('receiver', 'r', InputOption::VALUE_REQUIRED, 'Test receiver.')
            ->addOption('page', 'p', InputOption::VALUE_REQUIRED, 'Page number.', 1)
            ->addOption('pack', 'k', InputOption::VALUE_REQUIRED, 'Pack size.', 20);
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $set = $input->getArgument('set');
        $output->writeln(sprintf('Sending %s type supplement links to %s.', $type, $set));

        $this->isTest = $input->getOption('test');
        $this->receiver = $input->getOption('receiver');
        $this->limit = +$input->getOption('pack');
        $this->offset = $this->limit * ($input->getOption('page') - 1);

        $container = $this->getContainer();
        $emailAliases = $container->getParameter('wyd2016.email_alias');
        $this->emailAlias = $emailAliases[$type];
        $wydPage = $container->getParameter('wyd2016.wyd_page');
        $this->loginPage = $wydPage['login'];

        if ($type == self::TYPE_VOLUNTEER) {
            if ($set == self::SET_VOLUNTEERS) {
                $countVolunteers = $this->executeAdultVolunteers($output);
                $output->writeln(sprintf('%d e-mails have been sent to adult volunteers.', $countVolunteers));
            } elseif ($set == self::SET_TROOPS) {
                $countTroops = $this->executeTroops($output);
                $output->writeln(sprintf('%d e-mails have been sent to troop leaders.', $countTroops));
            } else {
                $output->writeln(sprintf('Selected set (%s) for type %s is inproper.', $set, $type));
            }
        } else {
            $output->writeln(sprintf('Selected type (%s) is inproper.', $type));
        }
    }

    /**
     * Execute adult volunteers
     *
     * @param OutputInterface $output output
     *
     * @return integer
     */
    protected function executeAdultVolunteers(OutputInterface $output)
    {
        $container = $this->getContainer();
        $volunteers = $container->get('wyd2016bundle.volunteer.repository')
            ->findBy(array(
                'troop' => null,
            ), array(
                'id' => 'ASC',
            ), $this->limit, $this->offset);

        $count = 0;
        /** @var Volunteer $volunteer */
        foreach ($volunteers as $volunteer) {
            $list = array(
                $volunteer,
            );
            $supplementVolunteers = $this->getSupplementVolunteers($list);
            $wydFormVolunteers = $this->getWydFormVolunteers($list);
            if (count($supplementVolunteers) < 1 && count($wydFormVolunteers) < 1) {
                continue;
            }

            $output->write(sprintf('Sending e-mail to volunteer %s...', $volunteer->getName()));
            if ($this->isTest) {
                $output->writeln(' [TEST - OK]');
                continue;
            }

            $translator = $container->get('translator');
            $locale = $this->getLocale($volunteer->getCountry());
            $email = empty($this->receiver) ? $volunteer->getEmail() : $this->receiver;
            $translator->setLocale($locale);
            $title = $translator->trans('supplement.title');
            $template = 'Wyd2016Bundle::supplement/volunteer_email.html.twig';
            $count += $this->sendMail($output, $email, $title, $template, array(
                'emailAlias' => $this->emailAlias,
                'locale' => $locale,
                'loginPage' => $this->loginPage,
                'supportSupplement' => count($supplementVolunteers) == 1,
                'supportWydForm' => count($wydFormVolunteers) == 1,
                'volunteer' => $volunteer,
            ));
        }

        return $count;
    }

    /**
     * Execute troops
     *
     * @param OutputInterface $output output
     *
     * @return integer
     */
    protected function executeTroops(OutputInterface $output)
    {
        $container = $this->getContainer();
        $troops = $container->get('wyd2016bundle.troop.repository')
            ->findBy(array(), array(
                'id' => 'ASC',
            ), $this->limit, $this->offset);

        $count = 0;
        /** @var Troop $troop */
        foreach ($troops as $troop) {
            $members = $troop->getMembers()
                ->toArray();
            $supplementVolunteers = $this->getSupplementVolunteers($members);
            $wydFormVolunteers = $this->getWydFormVolunteers($members);
            if (count($supplementVolunteers) < 1 && count($wydFormVolunteers) < 1) {
                continue;
            }

            $leader = $troop->getLeader();
            if (!isset($leader)) {
                $output->writeln(sprintf('Troop %s has no leader!', $troop->getName()));
                continue;
            }

            $output->write(sprintf('Sending e-mail to troop %s...', $troop->getName()));
            if ($this->isTest) {
                $output->writeln(' [TEST - OK]');
                continue;
            }

            $translator = $container->get('translator');
            $locale = $this->getLocale($leader->getCountry());
            $email = empty($this->receiver) ? $leader->getEmail() : $this->receiver;
            $translator->setLocale($locale);
            $title = $translator->trans('supplement.title');
            $template = 'Wyd2016Bundle::supplement/troop_email.html.twig';
            $count += $this->sendMail($output, $email, $title, $template, array(
                'emailAlias' => $this->emailAlias,
                'locale' => $locale,
                'loginPage' => $this->loginPage,
                'supplementVolunteers' => $supplementVolunteers,
                'wydFormVolunteers' => $wydFormVolunteers,
            ));
        }

        return $count;
    }

    /**
     * Get supplement volunteers
     *
     * @param Volunteer[] $volunteers volunteers
     *
     * @return array
     */
    protected function getSupplementVolunteers(array $volunteers)
    {
        $supplementManager = $this->getContainer()
            ->get('wyd2016bundle.manager.supplement');

        $list = array();
        foreach ($volunteers as $volunteer) {
            $supplement = $supplementManager->getVolunteerSupplement($volunteer);
            if ($supplement->ifAskForAnything()) {
                $list[] = $volunteer;
            }
        }

        return $list;
    }

    /**
     * Get WYD form volunteers
     *
     * @param Volunteer[] $volunteers volunteers
     *
     * @return array
     */
    protected function getWydFormVolunteers(array $volunteers)
    {
        $list = array();
        foreach ($volunteers as $volunteer) {
            if ($volunteer->getWydFormPassword()) {
                $list[] = $volunteer;
            }
        }

        return $list;
    }

    /**
     * Executes region, returns 1 if emails have been sent, 0 otherwise
     *
     * @param OutputInterface $output   output
     * @param string          $email    email
     * @param string          $title    title
     * @param string          $template template
     * @param array           $params   params
     *
     * @return integer
     */
    protected function sendMail(OutputInterface $output, $email, $title, $template, array $params)
    {
        $container = $this->getContainer();
        $body = $container->get('templating')
            ->render($template, $params);

        $message = Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom($container->getParameter('mailer_user'))
            ->setTo($email)
            ->setBody($body, 'text/html');
        $mailer = $container->get('mailer');

        if ($mailer->send($message)) {
            $output->writeln(sprintf(' [OK]', $email));
            $sent = 1;
        } else {
            $output->writeln(sprintf(' [an error occured during sending an e-mail to %s]', $email));
            $sent = 0;
        }

        return $sent;
    }

    /**
     * Get locale
     *
     * @param string $country country
     *
     * @return string
     */
    protected function getLocale($country)
    {
        $locale = strtolower($country);
        $container = $this->getContainer();
        if (!in_array($locale, $container->getParameter('locales'))) {
            $locale = $container->getParameter('locale');
        }

        return $locale;
    }
}
