<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Appstore\Bundle\HumanResourceBundle\Form\UserType;
use Core\UserBundle\Entity\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmployeeProfileType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    /** @var  GlobalOption */
    private $option;

    /** @var  UserRepository */
    private $user;

    function __construct(  UserRepository $user , GlobalOption $option , LocationRepository $location)
    {
        $this->location = $location;
        $this->option = $option;
        $this->user = $user;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter Employee Full Name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please Enter Employee Full Name'))
                    ))
            )
            ->add('fatherName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Employee Father Name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please Enter Father Name'))
                    ))
            )
            ->add('motherName','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Employee Mother Name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please Enter Mother Name'))
                    ))
            )
            ->add('facebookId','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Employee Facebook ID'))
            )
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','autocomplete'=>'off','placeholder'=>'Enter Mobile no'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please Enter Mobile no'))
                    ))
            )
             ->add('phoneNo','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Phone no'),
                    )
            )
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 ','rows'=>2,'placeholder'=>'Enter Employee Address'),
                    'constraints' =>array( new NotBlank(array('message'=>'Please Employee Address')))
                )
            )
            ->add('permanentAddress','textarea', array('attr'=>array('class'=>'m-wrap span12 ','rows'=>2,'placeholder'=>'Enter Employee Permanent Address'))
            )
            ->add('joiningDate', 'date', array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y')-50),
            ))
            ->add('dob', 'date', array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y')-100),
            ))
            ->add('designation', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Designation',
                'empty_value' => '---Choose a designation---',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Employee Designation'))
                ),
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status =1")
                        ->orderBy("e.name", "ASC");
                },
            ))
            ->add('nid','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter national id card no')))
            ->add('department', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\HumanResourceBundle\Entity\HrDepartment',
                'empty_value' => '---Choose a department Name---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->option->getId())
                        ->andWhere("e.status =1")
                        ->orderBy("e.name", "ASC");
                },
            ))
            ->add('maritalStatus', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => 'Marital Status',
                'choices' => array('Married' => 'Married','Un-married' => 'Un-married','Single' => 'Single'),
            ))
            ->add('bloodGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => 'Blood Group',
                'choices' => array('A+' => 'A+',  'A-' => 'A-','B+' => 'B+',  'B-' => 'B-',  'O+' => 'O+',  'O-' => 'O-',  'AB+' => 'AB+',  'AB-' => 'AB-'),
            ))
            ->add('religionStatus', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    'Islam' => 'Islam',
                    'Hinduism' => 'Hinduism',
                    'Buddhism' => 'Buddhism',
                    'Christianity' => 'Christianity',
                    'Other religions' => 'Other religions',
                ),
            ))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))
            ->add('file')
            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    'Male' => 'Male',
                    'Female' => 'Female'
                ),
            ))
            ->add('employeeType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Employee Type'))
                ),
                'empty_value' => '---Select Employee Type---',
                'choices' => array(
                    'Full Time' => 'Full Time',
                    'Half Time' => 'Half Time',
                    'Contractual' => 'Contractual',
                    'Hourly' => 'Hourly',
                ),
            ));

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\Profile'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'customer';
    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
