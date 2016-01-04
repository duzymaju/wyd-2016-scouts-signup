<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Form\RegistrationLists;

/*
 * Form Type
 */
class TroopType extends AbstractType
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $locale;

    /** @var RegistrationLists */
    protected $registrationLists;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator        translator
     * @param string              $locale            locale
     * @param RegistrationLists   $registrationLists registration lists
     */
    public function __construct(TranslatorInterface $translator, $locale, RegistrationLists $registrationLists)
    {
        $this->translator = $translator;
        $this->locale = $locale;
        $this->registrationLists = $registrationLists;
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
            'choices' => $this->registrationLists->getRegions(),
            'label' => $this->translator->trans('form.region'),
            'mapped' => false,
        ))
        ->add('districtId', 'choice', array(
            'choices' => $this->registrationLists->getDistricts(),
            'label' => $this->translator->trans('form.district'),
            'mapped' => false,
        ))
        ->add('serviceMainId', 'choice', array(
            'choices' => $this->registrationLists->getServices(),
            'label' => $this->translator->trans('form.serviceMain'),
            'mapped' => false,
        ))
        ->add('serviceExtraId', 'choice', array(
            'choices' => $this->registrationLists->getServices(),
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
            'type' => new TroopMemberType($this->translator, $this->registrationLists),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ))
        ->add('dateFrom', 'date', array(
            'label' => $this->translator->trans('form.date_from'),
            'widget' => 'single_text',
        ))
        ->add('dateTo', 'date', array(
            'label' => $this->translator->trans('form.date_to'),
            'widget' => 'single_text',
        ))
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