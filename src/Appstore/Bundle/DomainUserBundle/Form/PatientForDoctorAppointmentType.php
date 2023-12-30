<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PatientForDoctorAppointmentType extends AbstractType
{


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
            ->add('mobile','number', array('attr'=>array('class'=>'m-wrap span12  inputs','autocomplete'=>'off','placeholder'=>'Enter valid mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter valid mobile no')),
                )
            ))
            ->add('age','number', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Enter valid age'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Patient age')),
            )))

            ->add('height','number', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Height')))
            ->add('weight','number', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'Weight')))
            ->add('bloodPressure','text', array('attr'=>array('class'=>'m-wrap span12 numeric inputs patientAge','placeholder'=>'B/P')))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 numeric inputs','placeholder'=>'Address')))

            ->add('ageType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap inputs ageType'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Years' => 'Years','Months' => 'Months','Day' => 'Day')
            ))
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap inputs gender'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Female' => 'Female','Male' => 'Male', 'Others' => 'Others'),
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

}
