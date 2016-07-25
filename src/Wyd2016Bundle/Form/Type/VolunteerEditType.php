<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/*
 * Form type
 */
class VolunteerEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options);

        $builder->add('status', 'choice', $this->mergeOptions('status', array(
            'choices' => $this->registrationLists->getStatuses(),
            'label' => $this->translator->trans('form.status'),
        )))
        ->add('emergencyInfo', 'text', $this->mergeOptions('emergencyInfo', array(
            'label' => $this->translator->trans('form.emergency_info'),
            'required' => false,
        )))
        ->add('emergencyPhone', 'text', $this->mergeOptions('emergencyPhone', array(
            'label' => $this->translator->trans('form.emergency_phone'),
            'required' => false,
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
        return 'volunteer_edit';
    }
}
