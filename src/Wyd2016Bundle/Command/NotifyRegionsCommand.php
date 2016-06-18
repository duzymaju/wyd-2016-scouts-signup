<?php

namespace Wyd2016Bundle\Command;

use DateTime;
use InvalidArgumentException;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Form\RegistrationLists;

/**
 * Command
 */
class NotifyRegionsCommand extends ContainerAwareCommand
{
    /** @var string */
    const PERIOD_CUSTOM = 'custom';

    /** @var string */
    const PERIOD_DAY = 'day';

    /** @var string */
    const PERIOD_MONTH = 'month';

    /** @var string */
    const PERIOD_WEEK = 'week';

    /** @var array */
    protected $periods = array(
        self::PERIOD_CUSTOM,
        self::PERIOD_DAY,
        self::PERIOD_MONTH,
        self::PERIOD_WEEK,
    );

    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('regions:notify')
            ->setDescription('Notify region headquarters about new volunteers from their regions.')
            ->addOption('period', 'p', InputOption::VALUE_REQUIRED, 'Time period to search in.', self::PERIOD_WEEK)
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED, 'Messages locale.', 'pl')
            ->addOption('receiver', 'r', InputOption::VALUE_REQUIRED, 'Test receiver.');
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $translator = $container->get('translator');
        $translator->setLocale($input->getOption('locale'));

        $period = $input->getOption('period');
        if (preg_match('#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#', $period)) {
            $timeFrom = new DateTime($period);
            $timeFrom->setTime(0, 0, 0);
            $timeTo = new DateTime('yesterday');
            $timeTo->setTime(23, 59, 59);
            if (!$timeFrom instanceof DateTime || $timeFrom >= $timeTo) {
                throw new InvalidArgumentException('Custom period is incorrect.');
            }
            $period = self::PERIOD_CUSTOM;
        }
        if (!in_array($period, $this->periods)) {
            throw new InvalidArgumentException(sprintf('Period %s doesn\'t exist.', $period));
        }

        switch ($period) {
            case self::PERIOD_CUSTOM:
                $date = $translator->trans('region_email.date.custom', array(
                    '%timeFrom%' => $timeFrom->format('Y-m-d'),
                    '%timeTo%' => $timeTo->format('Y-m-d'),
                ));
                $title = $translator->trans('region_email.title.custom');
                break;

            case self::PERIOD_DAY:
                $timeFrom = new DateTime('yesterday');
                $timeFrom->setTime(0, 0, 0);
                $timeTo = new DateTime('yesterday');
                $timeTo->setTime(23, 59, 59);
                $date = $translator->trans('region_email.date.day', array(
                    '%time%' => $timeFrom->format('Y-m-d'),
                ));
                $title = $translator->trans('region_email.title.day');
                break;

            case self::PERIOD_WEEK:
                $now = new DateTime();
                $difference = - 6 - $now->format('N');
                $timeFrom = clone $now->modify($difference . ' days');
                $timeFrom->setTime(0, 0, 0);
                $timeTo = clone $now->modify('+6 days');
                $timeTo->setTime(23, 59, 59);
                $date = $translator->trans('region_email.date.week', array(
                    '%timeFrom%' => $timeFrom->format('Y-m-d'),
                    '%timeTo%' => $timeTo->format('Y-m-d'),
                ));
                $title = $translator->trans('region_email.title.week');
                break;

            case self::PERIOD_MONTH:
            default:
                $timeFrom = new DateTime('first day of last month');
                $timeFrom->setTime(0, 0, 0);
                $timeTo = new DateTime('last day of last month');
                $timeTo->setTime(23, 59, 59);
                $date = $translator->trans('region_email.date.month', array(
                    '%timeFrom%' => $timeFrom->format('Y-m-d'),
                    '%timeTo%' => $timeTo->format('Y-m-d'),
                ));
                $title = $translator->trans('region_email.title.month');
                break;
        }

        $receiver = $input->getOption('receiver');
        $receivers = empty($receiver) || !is_string($receiver) ? null : (array) $receiver;

        /** @var RegistrationLists $registrationLists */
        $registrationLists = $container->get('wyd2016bundle.registration.lists');

        $descriptions = array(
            'commander' => $translator->trans('region_email.content.commander_title', array(
                '%date%' => $date,
            )),
            'coordinator' => $translator->trans('region_email.content.coordinator_title', array(
                '%date%' => $date,
            )),
        );

        $count = 0;
        foreach ($registrationLists->getStructure() as $regionId => $region) {
            foreach ($descriptions as $type => $description) {
                if (count($region['emails'][$type]) > 0) {
                    $count += $this->executeRegion($output, $timeFrom, $timeTo, $regionId,
                        isset($receivers) ? $receivers : $region['emails'][$type], $title, $description);
                }
            }
        }

        $output->writeln(sprintf('%d e-mails have been sent.', $count));
    }

    /**
     * Executes region, returns 1 if emails have been sent, 0 otherwise
     *
     * @param OutputInterface $output      output
     * @param DateTime        $timeFrom    time from
     * @param DateTime        $timeTo      time to
     * @param integer         $regionId    region ID
     * @param array           $emails      emails
     * @param string          $title       title
     * @param string          $description description
     *
     * @return integer
     */
    protected function executeRegion(OutputInterface $output, DateTime $timeFrom, DateTime $timeTo, $regionId, $emails,
        $title, $description)
    {
        $container = $this->getContainer();
        $volunteers = $container->get('wyd2016bundle.volunteer.repository')
            ->getByRegionAndTime($regionId, $timeFrom, $timeTo);

        $volunteersByDistricts = array();
        if (count($volunteers) > 0) {
            foreach ($volunteers as $volunteer) {
                /** @var Volunteer $volunteer */
                $districtId = $volunteer->getDistrictId();
                if (!array_key_exists($districtId, $volunteersByDistricts)) {
                    $volunteersByDistricts[$districtId] = array();
                }
                $volunteersByDistricts[$districtId][] = $volunteer;
            }
        }
        $body = $container->get('templating')
            ->render('Wyd2016Bundle::region/email.html.twig', array(
                'byDistricts' => $volunteersByDistricts,
                'description' => $description,
            ));

        $message = Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom($container->getParameter('mailer_user'))
            ->setTo($emails)
            ->setReplyTo($container->getParameter('wyd2016.email.reply_to'))
            ->setBody($body, 'text/html');
        $mailer = $container->get('mailer');

        if (!$mailer->send($message)) {
            $output->writeln(sprintf('An error occured during sending an e-mail to %s.', implode(', ', $emails)));
            $sent = 0;
        } else {
            $sent = 1;
        }

        return $sent;
    }
}
