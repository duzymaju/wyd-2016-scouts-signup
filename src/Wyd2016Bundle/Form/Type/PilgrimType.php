<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wyd2016Bundle\Form\RegistrationLists;

/*
 * Form type
 */
class PilgrimType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct(TranslatorInterface $translator, RegistrationLists $registrationLists)
    {
        parent::__construct($translator, $registrationLists);
        $this->loadValidation('Pilgrim');
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
        ->add('birthDate', 'date', $this->mergeOptions('birthDate', array(
            'label' => $this->translator->trans('form.birth_date'),
            'widget' => 'single_text',
        )))
        ->add('sex', 'choice', $this->mergeOptions('sex', array(
            'choices' => $this->registrationLists->getSexes(),
            'label' => $this->translator->trans('form.sex'),
        )))
        ->add('shirtSize', 'choice', $this->mergeOptions('shirtSize', array(
            'choices' => $this->registrationLists->getShirtSizes(),
            'label' => $this->translator->trans('form.shirt_size'),
        )))
        ->add('datesId', 'choice', $this->mergeOptions('datesId', array(
            'choices' => $this->registrationLists->getPilgrimDates(),
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
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pilgrim';
    }
}
