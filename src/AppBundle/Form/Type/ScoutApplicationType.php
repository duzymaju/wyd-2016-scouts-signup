<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/*
 * Form Type
 */
class ScoutApplicationType extends AbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name', 'text')
            ->add('last_name', 'text')
            ->add('save', 'submit');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'scout_application';
    }
}
