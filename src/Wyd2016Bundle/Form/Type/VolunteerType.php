<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wyd2016Bundle\Form\DataTransformer\LanguagesCollectionTransformer;
use Wyd2016Bundle\Form\DataTransformer\PermissionsCollectionTransformer;
use Wyd2016Bundle\Form\RegistrationLists;

/*
 * Form type
 */
class VolunteerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct(TranslatorInterface $translator, RegistrationLists $registrationLists)
    {
        parent::__construct($translator, $registrationLists);
        $this->loadValidation('Volunteer');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options);

        $builder->add('firstName', 'text', $this->mergeOptions('firstName', array(
            'label' => $this->translator->trans('form.first_name'),
        )))
        ->add('lastName', 'text', $this->mergeOptions('lastName', array(
            'label' => $this->translator->trans('form.last_name'),
        )))
        ->add('address', 'text', $this->mergeOptions('address', array(
            'label' => $this->translator->trans('form.address'),
        )))
        ->add('phone', 'text', $this->mergeOptions('phone', array(
            'label' => $this->translator->trans('form.phone'),
        )))
        ->add('email', 'email', $this->mergeOptions('email', array(
            'label' => $this->translator->trans('form.email'),
        )))
        ->add('country', 'country', $this->mergeOptions('country', array(
            'label' => $this->translator->trans('form.country'),
            'preferred_choices' => array(
                strtoupper($this->locale),
            ),
        )))
        ->add('associationName', 'text', $this->mergeOptions('associationName', array(
            'label' => $this->translator->trans('form.association_name'),
            'required' => false,
        )))
        ->add('birthDate', 'date', $this->mergeOptions('birthDate', array(
            'label' => $this->translator->trans('form.birth_date'),
            'required' => false,
            'widget' => 'single_text',
        )))
        ->add('sex', 'choice', $this->mergeOptions('sex', array(
            'choices' => $this->registrationLists->getSexes(),
            'label' => $this->translator->trans('form.sex'),
        )))
        ->add('gradeId', 'choice', $this->mergeOptions('gradeId', array(
            'choices' => $this->registrationLists->getGrades(),
            'label' => $this->translator->trans('form.grade'),
        )))
        ->add('regionId', 'choice', $this->mergeOptions('regionId', array(
            'choices' => $this->registrationLists->getRegions(),
            'label' => $this->translator->trans('form.region'),
        )))
        ->add('districtId', 'choice', $this->mergeOptions('districtId', array(
            'choices' => $this->registrationLists->getDistricts(),
            'label' => $this->translator->trans('form.district'),
        )))
        ->add('pesel', 'text', $this->mergeOptions('pesel', array(
            'label' => $this->translator->trans('form.pesel'),
            'required' => false,
        )))
        ->add('fatherName', 'text', $this->mergeOptions('fatherName', array(
            'label' => $this->translator->trans('form.father_name'),
        )))
        ->add('shirtSize', 'choice', $this->mergeOptions('shirtSize', array(
            'choices' => $this->registrationLists->getShirtSizes(),
            'label' => $this->translator->trans('form.shirt_size'),
        )))
        ->add('serviceMainId', 'choice', $this->mergeOptions('serviceMainId', array(
            'choices' => $this->registrationLists->getServices(),
            'label' => $this->translator->trans('form.service_main'),
        )))
        ->add('serviceExtraId', 'choice', $this->mergeOptions('serviceExtraId', array(
            'choices' => $this->registrationLists->getServices(),
            'label' => $this->translator->trans('form.service_extra'),
        )))
        ->add('languages', 'choice', $this->mergeOptions('languages', array(
            'choices' => $this->registrationLists->getLanguages(),
            'expanded' => true,
            'label' => $this->translator->trans('form.languages'),
            'multiple' => true,
            'required' => false,
        )))
        ->add('permissions', 'choice', $this->mergeOptions('permissions', array(
            'choices' => $this->registrationLists->getPermissions(),
            'expanded' => true,
            'label' => $this->translator->trans('form.permissions'),
            'multiple' => true,
            'required' => false,
        )))
        ->add('otherPermissions', 'text', $this->mergeOptions('otherPermissions', array(
            'label' => $this->translator->trans('form.other_permissions'),
            'required' => false,
        )))
        ->add('profession', 'text', $this->mergeOptions('profession', array(
            'label' => $this->translator->trans('form.profession'),
            'required' => false,
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
            'constraints' => array(
                new NotBlank(),
            ),
            'label' => $this->translator->trans('form.personal_data'),
            'mapped' => false,
        )))
        ->add('rules', 'checkbox', $this->mergeOptions('rules', array(
            'constraints' => array(
                new NotBlank(),
            ),
            'mapped' => false,
        )))
        ->add('save', 'submit', array(
            'label' => $this->translator->trans('form.save'),
        ));

        $builder->get('languages')
            ->addModelTransformer(new LanguagesCollectionTransformer());
        $builder->get('permissions')
            ->addModelTransformer(new PermissionsCollectionTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'volunteer';
    }
}
