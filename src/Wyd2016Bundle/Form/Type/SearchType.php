<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/*
 * Form type
 */
class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options);

        $builder->add('type', 'choice', array(
            'attr' => array(
                'class' => 'form-control',
            ),
            'choices' => array(
                'volunteer' => $this->translator->trans('admin.volunteer'),
                'pilgrim' => $this->translator->trans('admin.pilgrim'),
                'troop' => $this->translator->trans('admin.troop'),
                'group' => $this->translator->trans('admin.group'),
            ),
            'label' => $this->translator->trans('admin.search.type'),
        ))->add('query', 'text', array(
            'attr' => array(
                'class' => 'form-control',
            ),
            'label' => $this->translator->trans('admin.search.query'),
        ))
        ->add('search', 'submit', array(
            'attr' => array(
                'class' => 'btn btn-primary',
            ),
            'label' => $this->translator->trans('admin.search.submit'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'search';
    }
}
