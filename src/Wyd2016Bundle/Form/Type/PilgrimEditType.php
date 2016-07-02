<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/*
 * Form type
 */
class PilgrimEditType extends AbstractType
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
        ->add('save', 'submit', array(
            'label' => $this->translator->trans('form.save'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pilgrim_edit';
    }
}
