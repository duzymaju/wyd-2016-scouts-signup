<?php

namespace Wyd2016Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wyd2016Bundle\Entity\Repository\BaseRepositoryInterface;
use Wyd2016Bundle\Entity\Repository\PilgrimRepository;
use Wyd2016Bundle\Entity\Repository\VolunteerRepository;
use Wyd2016Bundle\Model\PersonInterface;

/**
 * Command
 */
class FormatDataCommand extends ContainerAwareCommand
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('data:format')
            ->setDescription('Format volunteer/pilgrim data.')
            ->addOption('update', 'u', InputOption::VALUE_NONE, 'Updates formatted data.')
            ->addOption('countryCodeAdd', 'c', InputOption::VALUE_NONE, 'Adds area code for Polish phone numbers.');
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $container = $this->getContainer();
        $counter = 0;

        /** @var VolunteerRepository $volunteerRepository */
        $volunteerRepository = $container->get('wyd2016bundle.volunteer.repository');
        $counter += $this->formatPeopleData($volunteerRepository->findAll(), $volunteerRepository);

        /** @var PilgrimRepository $pilgrimRepository */
        $pilgrimRepository = $container->get('wyd2016bundle.pilgrim.repository');
        $counter += $this->formatPeopleData($pilgrimRepository->findAll(), $pilgrimRepository);

        if ($this->input->getOption('update')) {
            $volunteerRepository->flush();
            $pilgrimRepository->flush();
            $this->output->writeln(sprintf('%d records have been updated.', $counter));
        } else {
            $this->output->writeln(sprintf('%d records have been selected to update.', $counter));
        }
    }

    /**
     * Format people data
     *
     * @param array                   $people     people
     * @param BaseRepositoryInterface $repository repository
     *
     * @return integer
     */
    protected function formatPeopleData(array $people, BaseRepositoryInterface $repository)
    {
        $counter = 0;
        $update = $this->input->getOption('update');
        $countryCodeAdd = $this->input->getOption('countryCodeAdd');
        /** @var PersonInterface $person */
        foreach ($people as $person) {
            $change = false;
            $isPoland = $person->getCountry() == 'PL';

            if ($this->checkPhone($person, $isPoland, $countryCodeAdd)) {
                $change = true;
            }

            if ($change) {
                if ($update) {
                    $repository->update($person);
                }
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * Check phone
     *
     * @param PersonInterface $person         person
     * @param boolean         $isPoland       is Poland
     * @param boolean         $countryCodeAdd country code add
     *
     * @return boolean
     */
    protected function checkPhone(PersonInterface $person, $isPoland, $countryCodeAdd)
    {
        $change = false;

        $phone1 = preg_replace('#[^\+0-9]#', '', $person->getPhone());
        if ($phone1 != $person->getPhone()) {
            $change = $this->changePhone($person, $phone1);
        }

        $phone2 = preg_replace('#^00#', '+', $person->getPhone());
        if ($phone2 != $person->getPhone()) {
            $change = $this->changePhone($person, $phone2);
        }

        if ($isPoland) {
            if ($countryCodeAdd && strpos($person->getPhone(), '+48') === false) {
                $change = $this->changePhone($person, '+48' . $person->getPhone());
            }

            $numberLength = strlen($person->getPhone());
            if ($numberLength != 12 && ($countryCodeAdd || $numberLength != 9)) {
                $this->output->writeln(sprintf('PHONE WARNING: wrong number length in %s ', $person->getPhone()));
            }
        }

        if (strpos($person->getPhone(), '+') !== 0) {
            $this->output->writeln(sprintf('PHONE WARNING: no country calling code in %s ', $person->getPhone()));
        }

        return $change;
    }

    /**
     * Change phone
     *
     * @param PersonInterface $person person
     * @param string          $phone  phone
     *
     * @return boolean
     */
    protected function changePhone(PersonInterface $person, $phone)
    {
        $this->output->writeln(sprintf('PHONE CHANGE: %s -> %s', $person->getPhone(), $phone));
        $person->setPhone($phone);

        return true;
    }
}
