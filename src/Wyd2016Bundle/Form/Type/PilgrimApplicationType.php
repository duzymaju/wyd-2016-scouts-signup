<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Model\PilgrimApplication;

/*
 * Form Type
 */
class PilgrimApplicationType extends AbstractType
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
        ->add('country', 'country', array(
            'label' => $this->translator->trans('form.country'),
            'preferred_choices' => array(
                strtoupper($this->locale),
            ),
        ))
        ->add('address', 'text', array(
            'label' => $this->translator->trans('form.address'),
        ))
        ->add('phone', 'text', array(
            'label' => $this->translator->trans('form.phone'),
        ))
        ->add('mail', 'email', array(
            'label' => $this->translator->trans('form.mail'),
        ))
        ->add('accomodationId', 'choice', array(
            'choices' => array(
                PilgrimApplication::ACCOMODATION_SCHOOL =>
                    $this->translator->trans('form.accomodation.school'),
                PilgrimApplication::ACCOMODATION_TENT =>
                    $this->translator->trans('form.accomodation.tent'),
            ),
            'label' => $this->translator->trans('form.accomodation'),
        ))
        ->add('dateFrom', 'date', array_merge($dateOptions, array(
            'label' => $this->translator->trans('form.date_from'),
        )))
        ->add('dateTo', 'date', array_merge($dateOptions, array(
            'label' => $this->translator->trans('form.date_to'),
        )))
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
        return 'pilgrim_application';
    }
}
