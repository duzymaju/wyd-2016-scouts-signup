<?php

namespace Wyd2016Bundle\Form;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form
 */
class RegistrationLists
{
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get services
     *
     * @return array
     */
    public function getServices()
    {
        $services = array(
            7 => $this->translator->trans('form.service.kitchen'),
            6 => $this->translator->trans('form.service.office'),
            4 => $this->translator->trans('form.service.information'),
            3 => $this->translator->trans('form.service.quartermaster'),
            5 => $this->translator->trans('form.service.program'),
            1 => $this->translator->trans('form.service.medical'),
            2 => $this->translator->trans('form.service.security'),
        );

        return $services;
    }

    /**
     * Get regions
     *
     * @return array
     */
    public function getRegions()
    {
        $regions = array(
            // nothing to translate
            1 => 'Białostocka',
            2 => 'Dolnośląska',
            3 => 'Gdańska',
            4 => 'Kielecka',
            5 => 'Krakowska',
            6 => 'Kujawsko-Pomorska',
            7 => 'Lubelska',
            8 => 'Łódzka',
            9 => 'Mazowiecka',
            10 => 'Opolska',
            11 => 'Podkarpacka',
            12 => 'Stołeczna',
            13 => 'Śląska',
            14 => 'Warmińsko-Mazurska',
            15 => 'Wielkopolska',
            16 => 'Zachodniopomorska',
            17 => 'Ziemi Lubuskiej',
        );

        return $regions;
    }

    /**
     * Get districts
     *
     * @return array
     */
    public function getDistricts()
    {
        $districts = array(
            // nothing to translate
            1 => '[lista hufców]',
        );

        return $districts;
    }

    /**
     * Get grades
     *
     * @return array
     */
    public function getGrades()
    {
        $grades = array(
            0 => $this->translator->trans('form.grade.no'),
            1 => $this->translator->trans('form.grade.guide'),
            2 => $this->translator->trans('form.grade.sub_scoutmaster'),
            3 => $this->translator->trans('form.grade.scoutmaster'),
        );

        return $grades;
    }
}
