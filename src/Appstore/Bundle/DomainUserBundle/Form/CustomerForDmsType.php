<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerForDmsType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    function __construct(LocationRepository $location)
    {
        $this->location         = $location;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 inputs patientName','autocomplete'=>'off','placeholder'=>'Enter patient name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient name')),
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 inputs mobile','autocomplete'=>'off','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient mobile no')),
                )
            ))
            ->add('age','number', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Enter age'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Patient age')),
            )))
            ->add('weight','number', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Enter weight')))
             ->add('ageType', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom inputs ageType'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Years' => 'Years','Months' => 'Months','Day' => 'Day')
            ))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom inputs gender'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Female' => 'Female','Male' => 'Male', 'Others' => 'Others'),
            ))
            ->add('ageGroup', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom inputs'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Adult' => 'Adult','Child' => 'Child'),
            ))
            ->add('address','textarea', array('attr'=>array('required'=>false ,'class'=>'m-wrap span12 resize inputs address','rows'=> 3,'autocomplete'=>'off','placeholder'=>'Enter patient address')
            ))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12 location'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_domainuserbundle_customer';
    }

    protected function LocationChoiceList()
    {
        return $this->location->getLocationOptionGroup();

    }

}
