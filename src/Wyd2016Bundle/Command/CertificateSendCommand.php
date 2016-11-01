<?php

namespace Wyd2016Bundle\Command;

use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wyd2016Bundle\Model\Volunteer;

/**
 * Command
 */
class CertificateSendCommand extends ContainerAwareCommand
{
    /** @var array|null */
    protected $ids;

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
        $this->setName('certificate:send')
            ->setDescription('Notify volunteers about their certificates.')
            ->addOption('ids', 'i', InputOption::VALUE_REQUIRED, 'A list of IDs (divided by comma) to use.', null)
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
        $idsList = $input->getOption('ids');
        if (empty($idsList)) {
            $this->ids = null;
        } else {
            $ids = array();
            foreach (explode(',', $idsList) as $id) {
                if (is_numeric($id)) {
                    $ids[] = (integer) $id;
                }
            }
            $this->ids = array_unique($ids);
            sort($this->ids, SORT_NUMERIC);
        }

        $this->isTest = $input->getOption('test');
        $this->receiver = $input->getOption('receiver');
        $this->limit = +$input->getOption('pack');
        $this->offset = $this->limit * ($input->getOption('page') - 1);
        
        $output->writeln('Sending certificate links to volunteers.');
        $count = 0;
        $filePattern = $this->getContainer()
            ->getParameter('wyd2016.certificate_file_pattern');
        $volunteers = $this->findInBy('wyd2016bundle.volunteer.repository');
        /** @var Volunteer $volunteer */
        foreach ($volunteers as $volunteer) {
            $filePath = sprintf($filePattern, $volunteer->getId());
            if (file_exists($filePath)) {
                $count += $this->executeEmail($volunteer, $output);
            }
        }
        $output->writeln(sprintf('%d e-mails have been sent to volunteers.', $count));
    }

    /**
     * Execute volunteer
     *
     * @param Volunteer       $volunteer volunteer
     * @param OutputInterface $output    output
     *
     * @return integer
     */
    protected function executeEmail(Volunteer $volunteer, OutputInterface $output)
    {
        $output->write(sprintf('Sending e-mail to volunteer %d - %s...', $volunteer->getId(),
            $volunteer->getName()));
        if ($this->isTest) {
            $output->writeln(' [TEST - OK]');
            return 1;
        }

        $translator = $this->getContainer()
            ->get('translator');
        $locale = $this->getLocale($volunteer->getCountry());
        $email = empty($this->receiver) ? $volunteer->getEmail() : $this->receiver;
        $translator->setLocale($locale);
        $title = $translator->trans('certificate.title');
        $template = 'Wyd2016Bundle::certificate/email.html.twig';
        $count += $this->sendMail($output, $email, $title, $template, array(
            'locale' => $locale,
            'volunteer' => $volunteer,
        ));

        return $count;
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
            ->setReplyTo($container->getParameter('wyd2016.email.reply_to'))
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

    /**
     * Find in by
     *
     * @param string $repositoryServiceName repository service name
     * @param array  $criteria              criteria
     *
     * @return array
     */
    protected function findInBy($repositoryServiceName, array $criteria = array())
    {
        if ($this->ids) {
            $criteria['id'] = $this->ids;
        }

        $items = $this->getContainer()
            ->get($repositoryServiceName)
            ->findBy($criteria, array(
                'id' => 'ASC',
            ), $this->limit, $this->offset);

        return $items;
    }
}
