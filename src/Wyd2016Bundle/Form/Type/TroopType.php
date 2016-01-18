<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Form\RegistrationLists;

/*
 * Form type
 */
class TroopType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct(TranslatorInterface $translator, RegistrationLists $registrationLists)
    {
        parent::__construct($translator, $registrationLists);
        $this->loadValidation('Troop');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options);

        $builder->add('name', 'text', $this->mergeOptions('name', array(
            'label' => $this->translator->trans('form.troopName'),
        )))
        ->add('country', 'country', $this->mergeOptions('country', array(
            'label' => $this->translator->trans('form.country'),
            'mapped' => false,
            'preferred_choices' => array(
                strtoupper($this->locale),
            ),
        )))
        ->add('regionId', 'choice', $this->mergeOptions('regionId', array(
            'choices' => $this->registrationLists->getRegions(),
            'label' => $this->translator->trans('form.region'),
            'mapped' => false,
        )))
        ->add('districtId', 'choice', $this->mergeOptions('districtId', array(
            'choices' => $this->registrationLists->getDistricts(),
            'label' => $this->translator->trans('form.district'),
            'mapped' => false,
        )))
        ->add('languages', 'choice', $this->mergeOptions('languages', array(
            'choices' => $this->registrationLists->getLanguages(),
            'label' => $this->translator->trans('form.languages'),
            'mapped' => false,
            'multiple' => true,
            'required' => false,
        )))
        ->add('permissions', 'text', $this->mergeOptions('permissions', array(
            'label' => $this->translator->trans('form.permissions'),
            'mapped' => false,
            'required' => false,
        )))
        ->add('profession', 'text', $this->mergeOptions('profession', array(
            'label' => $this->translator->trans('form.profession'),
            'mapped' => false,
            'required' => false,
        )))
        ->add('members', 'collection', $this->mergeOptions('members', array(
            'allow_add' => true,
            'allow_delete' => false,
            'by_reference' => false,
            'type' => new TroopMemberType($this->translator, $this->registrationLists),
            'validation_groups' => array(
                'troopMember',
            ),
        )))
        ->add('datesId', 'choice', $this->mergeOptions('datesId', array(
            'choices' => $this->registrationLists->getVolunteerDates(),
            'label' => $this->translator->trans('form.dates'),
        )))
        ->add('comments', 'text', $this->mergeOptions('comments', array(
            'label' => $this->translator->trans('form.comments'),
            'required' => false,
        )))
        ->add('personalData', 'checkbox', $this->mergeOptions('personalData', array(
            'label' => $this->translator->trans('form.personal_data'),
            'mapped' => false,
        )))
        ->add('rules', 'checkbox', $this->mergeOptions('rules', array(
            'mapped' => false,
        )))
        ->add('save', 'submit', array(
            'label' => $this->translator->trans('form.save'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'data_class' => 'Wyd2016Bundle\Entity\Troop',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'troop';
    }
}
