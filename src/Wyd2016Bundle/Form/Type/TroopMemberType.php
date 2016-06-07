<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Form\RegistrationLists;

/*
 * Form type
 */
class TroopMemberType extends AbstractType
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
        ->add('birthDate', 'date', $this->mergeOptions('birthDate', array(
            'label' => $this->translator->trans('form.birth_date'),
            'required' => false,
            'widget' => 'single_text',
        )))
        ->add('sex', 'choice', $this->mergeOptions('sex', array(
            'choices' => $this->registrationLists->getSexes(),
            'label' => $this->translator->trans('form.sex'),
        )))
        ->add('pesel', 'text', $this->mergeOptions('pesel', array(
            'label' => $this->translator->trans('form.pesel'),
            'required' => false,
        )))
        ->add('fatherName', 'text', $this->mergeOptions('fatherName', array(
            'label' => $this->translator->trans('form.father_name'),
        )))
        ->add('gradeId', 'choice', $this->mergeOptions('gradeId', array(
            'choices' => $this->registrationLists->getGrades(),
            'label' => $this->translator->trans('form.grade'),
        )))
        ->add('shirtSize', 'choice', $this->mergeOptions('shirtSize', array(
            'choices' => $this->registrationLists->getShirtSizes(),
            'label' => $this->translator->trans('form.shirt_size'),
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wyd2016Bundle\Entity\Volunteer',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'troop_member';
    }
}
