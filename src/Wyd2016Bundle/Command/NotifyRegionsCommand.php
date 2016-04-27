<?php

namespace Wyd2016Bundle\Command;

use DateTime;
use InvalidArgumentException;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wyd2016Bundle\Entity\Volunteer;
use Wyd2016Bundle\Form\RegistrationLists;

/**
 * Command
 */
class NotifyRegionsCommand extends ContainerAwareCommand
{
    /** @var string */
    const PERIOD_DAY = 'day';

    /** @var string */
    const PERIOD_MONTH = 'month';

    /** @var string */
    const PERIOD_WEEK = 'week';

    /** @var array */
    protected $periods = array(
        self::PERIOD_MONTH,
        self::PERIOD_WEEK,
        self::PERIOD_DAY,
    );

    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('regions:notify')
            ->setDescription('Notify region headquarters about new volunteers from their regions.')
            ->addArgument('period', InputArgument::OPTIONAL, 'Time period to search in.', self::PERIOD_MONTH)
            ->addArgument('locale', InputArgument::OPTIONAL, 'Messages locale.', 'pl');
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
        $container->get('translator')
            ->setLocale($input->getArgument('locale'));

        $period = $input->getArgument('period');
        if (!in_array($period, $this->periods)) {
            throw new InvalidArgumentException(sprintf('Period %s doesn\'t exist.', $period));
        }

        switch ($period) {
            case self::PERIOD_DAY:
                $timeFrom = new DateTime('yesterday');
                $timeTo = new DateTime('yesterday');
                break;

            case self::PERIOD_WEEK:
                $now = new DateTime();
                $difference = - 6 - $now->format('N');
                $timeFrom = clone $now->modify($difference . ' days');
                $timeTo = clone $now->modify('+6 days');
                break;

            case self::PERIOD_MONTH:
            default:
                $timeFrom = new DateTime('first day of last month');
                $timeTo = new DateTime('last day of last month');
                break;
        }
        $timeFrom->setTime(0, 0, 0);
        $timeTo->setTime(23, 59, 59);

        /** @var RegistrationLists $registrationLists */
        $registrationLists = $container->get('wyd2016bundle.registration.lists');

        $count = 0;
        foreach ($registrationLists->getStructure() as $regionId => $region) {
            if (array_key_exists('email', $region) && !empty($region['email'])) {
                $count += $this->executeRegion($output, $timeFrom, $timeTo, $regionId, $region['name'],
                    $region['email'], $period);
            }
        }

        $output->writeln(sprintf('%d e-mails have been sent.', $count));
    }

    /**
     * Executes region, returns 1 if emails have been sent, 0 otherwise
     *
     * @param OutputInterface $output     output
     * @param DateTime        $timeFrom   time from
     * @param DateTime        $timeTo     time to
     * @param integer         $regionId   region ID
     * @param string          $email      email
     * @param string          $period     period
     *
     * @return integer
     */
    protected function executeRegion(OutputInterface $output, DateTime $timeFrom, DateTime $timeTo, $regionId,
        $email, $period)
    {
        $container = $this->getContainer();
        $volunteers = $container->get('wyd2016bundle.volunteer.repository')
            ->getByRegionAndTime($regionId, $timeFrom, $timeTo);

        $translator = $container->get('translator');
        switch ($period) {
            case self::PERIOD_DAY:
                $date = $translator->trans('region_email.date.day', array(
                    '%time%' => $timeFrom->format('Y-m-d'),
                ));
                $title = $translator->trans('region_email.title.day');
                break;

            case self::PERIOD_WEEK:
                $date = $translator->trans('region_email.date.week', array(
                    '%timeFrom%' => $timeFrom->format('Y-m-d'),
                    '%timeTo%' => $timeTo->format('Y-m-d'),
                ));
                $title = $translator->trans('region_email.title.week');
                break;

            case self::PERIOD_MONTH:
            default:
                $date = $translator->trans('region_email.date.month', array(
                    '%timeFrom%' => $timeFrom->format('Y-m-d'),
                    '%timeTo%' => $timeTo->format('Y-m-d'),
                ));
                $title = $translator->trans('region_email.title.month');
                break;
        }

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
                'date' => $date,
            ));

        $message = Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom($container->getParameter('mailer_user'))
            ->setTo($email)
            ->setBody($body, 'text/html');
        $mailer = $container->get('mailer');

        if (!$mailer->send($message)) {
            $output->writeln(sprintf('An error occured during sending an e-mail to %s.', $email));
            $sent = 0;
        } else {
            $sent = 1;
        }

        return $sent;
    }
}
