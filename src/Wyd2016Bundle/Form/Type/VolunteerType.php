<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Form\RegistrationLists;

/*
 * Form Type
 */
class VolunteerType extends AbstractType
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

        $builder->add('firstName', 'text', array(
            'label' => $this->translator->trans('form.first_name'),
        ))
        ->add('lastName', 'text', array(
            'label' => $this->translator->trans('form.last_name'),
        ))
        ->add('address', 'text', array(
            'label' => $this->translator->trans('form.address'),
        ))
        ->add('phone', 'text', array(
            'label' => $this->translator->trans('form.phone'),
        ))
        ->add('email', 'email', array(
            'label' => $this->translator->trans('form.email'),
        ))
        ->add('country', 'country', array(
            'label' => $this->translator->trans('form.country'),
            'preferred_choices' => array(
                strtoupper($this->locale),
            ),
        ))
        ->add('birthDate', 'date', array(
            'label' => $this->translator->trans('form.birth_date'),
            'required' => false,
            'widget' => 'single_text',
        ))
        ->add('gradeId', 'choice', array(
            'choices' => $this->registrationLists->getGrades(),
            'label' => $this->translator->trans('form.grade'),
        ))
        ->add('regionId', 'choice', array(
            'choices' => $this->registrationLists->getRegions(),
            'label' => $this->translator->trans('form.region'),
        ))
        ->add('districtId', 'choice', array(
            'choices' => $this->registrationLists->getDistricts(),
            'label' => $this->translator->trans('form.district'),
        ))
        ->add('pesel', 'text', array(
            'label' => $this->translator->trans('form.pesel'),
            'required' => false,
        ))
        ->add('serviceMainId', 'choice', array(
            'choices' => $this->registrationLists->getServices(),
            'label' => $this->translator->trans('form.serviceMain'),
        ))
        ->add('serviceExtraId', 'choice', array(
            'choices' => $this->registrationLists->getServices(),
            'label' => $this->translator->trans('form.serviceExtra'),
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
        ->add('ownTent', 'checkbox', array(
            'label' => $this->translator->trans('form.own_tent_volunteer'),
            'required' => false,
        ))
        ->add('datesId', 'choice', array(
            'choices' => $this->registrationLists->getDates(),
            'label' => $this->translator->trans('form.dates'),
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

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'volunteer';
    }
}
