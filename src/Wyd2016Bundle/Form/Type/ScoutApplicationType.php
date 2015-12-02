<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/*
 * Form Type
 */
class ScoutApplicationType extends AbstractType
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
     * Build form
     *
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options);

        $builder->add('firstName', 'text', array(
            'label' => $this->translator->trans('form.first_name'),
        ))
        ->add('lastName', 'text', array(
            'label' => $this->translator->trans('form.last_name'),
        ))
        ->add('gradeId', 'choice', array(
            'choices' => array(
                0 => $this->translator->trans('form.grade.no'),
                1 => $this->translator->trans('form.grade.guide'),
                2 => $this->translator->trans('form.grade.sub_scoutmaster'),
                3 => $this->translator->trans('form.grade.scoutmaster'),
            ),
            'label' => $this->translator->trans('form.grade'),
        ))
        ->add('regionId', 'choice', array(
            'choices' => array(
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
            ),
            'label' => $this->translator->trans('form.region'),
        ))
        ->add('pesel', 'text', array(
            'label' => $this->translator->trans('form.pesel'),
        ))
        ->add('address', 'text', array(
            'label' => $this->translator->trans('form.address'),
        ))
        ->add('serviceId', 'choice', array(
            'choices' => array(
                7 => $this->translator->trans('form.service.kitchen'),
                6 => $this->translator->trans('form.service.office'),
                4 => $this->translator->trans('form.service.information'),
                3 => $this->translator->trans('form.service.quartermaster'),
                5 => $this->translator->trans('form.service.program'),
                1 => $this->translator->trans('form.service.medical'),
                2 => $this->translator->trans('form.service.security'),
            ),
            'label' => $this->translator->trans('form.service'),
        ))
        ->add('permissions', 'text', array(
            'label' => $this->translator->trans('form.permissions'),
        ))
        ->add('languages', 'text', array(
            'label' => $this->translator->trans('form.languages'),
        ))
        ->add('profession', 'text', array(
            'label' => $this->translator->trans('form.profession'),
        ))
        ->add('phone', 'text', array(
            'label' => $this->translator->trans('form.phone'),
        ))
        ->add('email', 'email', array(
            'label' => $this->translator->trans('form.email'),
        ))
        ->add('serviceTime', 'text', array(
            'label' => $this->translator->trans('form.service_time'),
        ))
        ->add('save', 'submit', array(
            'label' => $this->translator->trans('form.save'),
        ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'scout_application';
    }
}
