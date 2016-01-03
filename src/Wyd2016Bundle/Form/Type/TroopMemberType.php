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
class TroopMemberType extends AbstractType
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var RegistrationLists */
    protected $registrationLists;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator        translator
     * @param RegistrationLists   $registrationLists registration lists
     */
    public function __construct(TranslatorInterface $translator, RegistrationLists $registrationLists)
    {
        $this->translator = $translator;
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
        ->add('birthDate', 'date', array(
            'label' => $this->translator->trans('form.birth_date'),
            'required' => false,
            'widget' => 'single_text',
        ))
        ->add('pesel', 'text', array(
            'label' => $this->translator->trans('form.pesel'),
            'required' => false,
        ))
        ->add('gradeId', 'choice', array(
            'choices' => $this->registrationLists->getGrades(),
            'label' => $this->translator->trans('form.grade'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wyd2016Bundle\Entity\Volunteer',
        ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'troop_member';
    }
}
