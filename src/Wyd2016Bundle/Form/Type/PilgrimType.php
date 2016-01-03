<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/*
 * Form Type
 */
class PilgrimType extends AbstractType
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $locale;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator translator
     * @param string              $locale     locale
     */
    public function __construct(TranslatorInterface $translator, $locale)
    {
        $this->translator = $translator;
        $this->locale = $locale;
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

        $dateOptions = array(
            'days' => range(17, 31),
            'months' => array(
                7,
            ),
            'years' => array(
                2016,
            ),
        );

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
        ->add('country', 'country', array(
            'label' => $this->translator->trans('form.country'),
            'preferred_choices' => array(
                strtoupper($this->locale),
            ),
        ))
        ->add('birthDate', 'date', array(
            'label' => $this->translator->trans('form.birth_date'),
            'widget' => 'single_text',
        ))
        ->add('dateFrom', 'date', array_merge($dateOptions, array(
            'label' => $this->translator->trans('form.date_from'),
            'widget' => 'single_text',
        )))
        ->add('dateTo', 'date', array_merge($dateOptions, array(
            'label' => $this->translator->trans('form.date_to'),
            'widget' => 'single_text',
        )))
        ->add('comments', 'text', array(
            'label' => $this->translator->trans('form.comments'),
            'required' => false,
        ))
        ->add('personalData', 'checkbox', array(
            'label' => $this->translator->trans('form.personal_data'),
            'mapped' => false,
        ))
        ->add('rules', 'checkbox', array(
            'mapped' => false,
        ))
        ->add('save', 'submit', array(
            'label' => $this->translator->trans('form.save'),
        ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'pilgrim';
    }
}
