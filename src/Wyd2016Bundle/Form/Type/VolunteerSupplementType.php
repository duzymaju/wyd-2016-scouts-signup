<?php

namespace Wyd2016Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Model\Virtual\VolunteerSupplement;
use Wyd2016Bundle\Model\Volunteer;

/*
 * Form type
 */
class VolunteerSupplementType extends AbstractType
{
    /** @var Volunteer */
    protected $volunteer;

    /** @var VolunteerSupplement */
    protected $supplement;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator          translator
     * @param RegistrationLists   $registrationLists   registration lists
     * @param Volunteer           $volunteer           volunteer
     * @param VolunteerSupplement $supplement          supplement
     */
    public function __construct(TranslatorInterface $translator, RegistrationLists $registrationLists,
        Volunteer $volunteer, VolunteerSupplement $supplement)
    {
        parent::__construct($translator, $registrationLists);
        $this->loadValidation('Volunteer');

        $this->volunteer = $volunteer;
        $this->supplement = $supplement;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        unset($options);

        if ($this->supplement->ifAskForDistrict()) {
            $builder->add('districtId', 'choice', $this->mergeOptions('districtId', array(
                'choices' => $this->registrationLists->getDistricts($this->volunteer->getRegionId()),
                'label' => $this->translator->trans('form.district'),
            )));
        }
        if ($this->supplement->ifAskForFatherName()) {
            $builder->add('fatherName', 'text', $this->mergeOptions('fatherName', array(
                'label' => $this->translator->trans('form.father_name'),
            )));
        }
        if ($this->supplement->ifAskForService()) {
            $builder->add('serviceMainId', 'choice', $this->mergeOptions('serviceMainId', array(
                'choices' => $this->registrationLists->getServices(),
                'label' => $this->translator->trans('form.service_main'),
            )))
            ->add('serviceExtraId', 'choice', $this->mergeOptions('serviceExtraId', array(
                'choices' => $this->registrationLists->getServices(),
                'label' => $this->translator->trans('form.service_extra'),
            )));
        }
        if ($this->supplement->ifAskForShirtSize()) {
            $builder->add('shirtSize', 'choice', $this->mergeOptions('shirtSize', array(
                'choices' => $this->registrationLists->getShirtSizes(),
                'label' => $this->translator->trans('form.shirt_size'),
            )));
        }
        if ($this->supplement->ifAskForDates()) {
            $builder->add('datesId', 'choice', $this->mergeOptions('datesId', array(
                'choices' => $this->registrationLists->getVolunteerDates(),
                'label' => $this->translator->trans('form.dates'),
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
