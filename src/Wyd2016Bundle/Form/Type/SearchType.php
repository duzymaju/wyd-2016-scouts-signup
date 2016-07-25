<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/*
 * Form type
 */
class SearchType extends AbstractType
{
    /** @const string */
    const CHOICE_ALL = 'all';

    /** @const string */
    const CHOICE_GROUP = 'group';

    /** @const string */
    const CHOICE_PILGRIM = 'pilgrim';

    /** @const string */
    const CHOICE_TROOP = 'troop';

    /** @const string */
    const CHOICE_VOLUNTEER = 'volunteer';

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
                self::CHOICE_ALL => $this->translator->trans('admin.all'),
                self::CHOICE_VOLUNTEER => $this->translator->trans('admin.volunteer'),
                self::CHOICE_PILGRIM => $this->translator->trans('admin.pilgrim'),
                self::CHOICE_TROOP => $this->translator->trans('admin.troop'),
                self::CHOICE_GROUP => $this->translator->trans('admin.group'),
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
