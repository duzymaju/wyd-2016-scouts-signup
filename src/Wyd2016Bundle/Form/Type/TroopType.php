<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/*
 * Form Type
 */
class TroopType extends AbstractType
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $locale;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator translator
     * @param string              $locale     locale
     */
    public function __construct(TranslatorInterface $translator, $locale)
    {
        $this->translator = $translator;
        $this->locale = $locale;
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

        $dateOptions = array(
            'days' => range(17, 31),
            'months' => array(
                7,
            ),
            'years' => array(
                2016,
            ),
        );

        $services = array(
            7 => $this->translator->trans('form.service.kitchen'),
            6 => $this->translator->trans('form.service.office'),
            4 => $this->translator->trans('form.service.information'),
            3 => $this->translator->trans('form.service.quartermaster'),
            5 => $this->translator->trans('form.service.program'),
            1 => $this->translator->trans('form.service.medical'),
            2 => $this->translator->trans('form.service.security'),
        );

        $builder->add('name', 'text', array(
            'label' => $this->translator->trans('form.troopName'),
        ))
        ->add('country', 'country', array(
            'label' => $this->translator->trans('form.country'),
            'mapped' => false,
            'preferred_choices' => array(
                strtoupper($this->locale),
            ),
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
            'mapped' => false,
        ))
        ->add('districtId', 'choice', array(
            'choices' => array(
                // nothing to translate
                1 => '[lista hufców]',
            ),
            'label' => $this->translator->trans('form.district'),
            'mapped' => false,
        ))
        ->add('serviceMainId', 'choice', array(
            'choices' => $services,
            'label' => $this->translator->trans('form.serviceMain'),
            'mapped' => false,
        ))
        ->add('serviceExtraId', 'choice', array(
            'choices' => $services,
            'label' => $this->translator->trans('form.serviceExtra'),
            'mapped' => false,
        ))
        ->add('permissions', 'text', array(
            'label' => $this->translator->trans('form.permissions'),
            'mapped' => false,
        ))
        ->add('languages', 'text', array(
            'label' => $this->translator->trans('form.languages'),
            'mapped' => false,
        ))
        ->add('profession', 'text', array(
            'label' => $this->translator->trans('form.profession'),
            'mapped' => false,
        ))
        ->add('members', 'collection', array(
            'type' => new TroopMemberType($this->translator),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ))
        ->add('dateFrom', 'date', array_merge($dateOptions, array(
            'label' => $this->translator->trans('form.date_from'),
            'widget' => 'single_text',
        )))
        ->add('dateTo', 'date', array_merge($dateOptions, array(
            'label' => $this->translator->trans('form.date_to'),
            'widget' => 'single_text',
        )))
        ->add('comments', 'text', array(
            'label' => $this->translator->trans('form.comments'),
            'required' => false,
        ))
        ->add('personalData', 'checkbox', array(
            'label' => $this->translator->trans('form.personal_data'),
            'mapped' => false,
        ))
        ->add('rules', 'checkbox', array(
            'mapped' => false,
        ))
        ->add('save', 'submit', array(
            'label' => $this->translator->trans('form.save'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wyd2016Bundle\Entity\Troop',
        ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'troop';
    }
}
