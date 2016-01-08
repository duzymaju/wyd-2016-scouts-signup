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
        ->add('permissions', 'text', array(
            'label' => $this->translator->trans('form.permissions'),
            'mapped' => false,
            'required' => false,
        ))
        ->add('profession', 'text', array(
            'label' => $this->translator->trans('form.profession'),
            'mapped' => false,
            'required' => false,
        ))
        ->add('members', 'collection', array(
            'allow_add' => false,
            'allow_delete' => false,
            'by_reference' => false,
            'type' => new TroopMemberType($this->translator, $this->registrationLists),
            'validation_groups' => array(
                'troopMember',
            ),
        ))
        ->add('ownTent', 'checkbox', array(
            'label' => $this->translator->trans('form.own_tent_group'),
            'required' => false,
        ))
        ->add('datesId', 'choice', array(
            'choices' => $this->registrationLists->getVolunteerDates(),
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
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
