<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DoctorType extends AbstractType
{


    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption)
    {

        $this->globalOption = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fees')))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter doctor designation')))
            ->add('phoneNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter additional phone no')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter valid email')))
            ->add('doctorPrescription','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>6,'placeholder'=>'Enter specialist')))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'End hour')))
            ->add('file')
            ->add('weeklyOffDay', 'choice', array(
                'attr'=>array('class'=>'check-list span12'),
                'expanded'      =>true,
                'multiple'      =>true,
                'choices' => array('Sunday' => 'Sunday',  'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'),
            ))
            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('assignDoctor', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select doctor from employee list---',
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userDoctor',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select doctor from employee list')),
                ),
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join('e.profile','p')
                        ->where("e.enabled = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                        ->andWhere('e.roles LIKE :roles')
                        ->setParameter('roles', '%"ROLE_DPS_DOCTOR"%')
                        ->orderBy("p.name","ASC");
                }
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular',
            'cascade_validation' => true,
            'error_bubbling'     => false,
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_particular';
    }


}
