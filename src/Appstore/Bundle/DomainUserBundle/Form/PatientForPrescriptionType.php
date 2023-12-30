<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PatientForPrescriptionType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('age','number', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Enter age'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Patient age')),
            )))
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 inputs patientAge','placeholder'=>'Enter patient name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter patient name')),
                )))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 inputs patientAge','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Patient mobile no')),
                )))

            ->add('weight','number', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Enter weight'),
                ))
            ->add('bloodPressure','text', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Enter Blood Pressure')))
            ->add('ageType', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom inputs ageType'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Years' => 'Years','Months' => 'Months','Day' => 'Day')
            ))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'empty_value' => '-Blood Group-',
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),
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
        return 'dp_patient';
    }


}
