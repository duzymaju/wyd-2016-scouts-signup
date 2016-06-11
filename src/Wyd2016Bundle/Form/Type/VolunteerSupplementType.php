<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Model\Volunteer;

/*
 * Form type
 */
class VolunteerSupplementType extends AbstractType
{
    /** @var Volunteer */
    protected $volunteer;

    /** @var boolean[] */
    protected $conditions;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator        translator
     * @param RegistrationLists   $registrationLists registration lists
     * @param Volunteer           $volunteer         volunteer
     * @param boolean[]           $conditions        conditions
     */
    public function __construct(TranslatorInterface $translator, RegistrationLists $registrationLists,
        Volunteer $volunteer, array $conditions)
    {
        parent::__construct($translator, $registrationLists);
        $this->loadValidation('Volunteer');

        $this->volunteer = $volunteer;
        $this->conditions = $conditions;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options);

        if (array_key_exists('district', $this->conditions) && $this->conditions['district']) {
            $builder->add('districtId', 'choice', $this->mergeOptions('districtId', array(
                'choices' => $this->registrationLists->getDistricts($this->volunteer->getRegionId()),
                'label' => $this->translator->trans('form.district'),
            )));
        }
        if (array_key_exists('fatherName', $this->conditions) && $this->conditions['fatherName']) {
            $builder->add('fatherName', 'text', $this->mergeOptions('fatherName', array(
                'label' => $this->translator->trans('form.father_name'),
            )));
        }
        if (array_key_exists('service', $this->conditions) && $this->conditions['service']) {
            $builder->add('serviceMainId', 'choice', $this->mergeOptions('serviceMainId', array(
                'choices' => $this->registrationLists->getServices(),
                'label' => $this->translator->trans('form.service_main'),
            )))
            ->add('serviceExtraId', 'choice', $this->mergeOptions('serviceExtraId', array(
                'choices' => $this->registrationLists->getServices(),
                'label' => $this->translator->trans('form.service_extra'),
            )));
        }
        if (array_key_exists('shirtSize', $this->conditions) && $this->conditions['shirtSize']) {
            $builder->add('shirtSize', 'choice', $this->mergeOptions('shirtSize', array(
                'choices' => $this->registrationLists->getShirtSizes(),
                'label' => $this->translator->trans('form.shirt_size'),
            )));
        }
        $builder->add('save', 'submit', array(
            'label' => $this->translator->trans('form.save'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'volunteer_supplement';
    }
}
