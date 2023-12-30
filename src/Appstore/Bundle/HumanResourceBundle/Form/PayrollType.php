<?php

namespace Appstore\Bundle\HumanResourceBundle\Form;


use Appstore\Bundle\DomainUserBundle\Form\EmployeeProfileType;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PayrollType extends AbstractType
{

    /** @var  GlobalOption */
    private $option;

    function __construct( GlobalOption $option)
    {
        $this->global = $option;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('effectedMonth','text', array('attr'=>array('class'=>'m-wrap span8 datePickerMonth','placeholder'=>'Enter Effected Month','autocomplete'=>'off'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input effected month')),
                )
            ))
            ->add('bonusApplicable')
            ->add('arearApplicable')
            ->add('salaryType', 'choice', array(
                'attr'=>array('class'=>'m-wrap span8'),
                'expanded'      =>false,
                'multiple'      =>false,
                 'constraints' =>array(
                     new NotBlank(array('message'=>'Please Choose Salary Type')),
                 ),
                'empty_value' => '-Choose Salary Type-',
                'choices' => array(
                    'fixed' => 'Fixed',
                    'honourable' => 'Honourable',
                    'day' => 'Day',
                    'hour' => 'Hour',
                ),
            ))
            /*->add('employeeType', 'choice', array(
                'attr'=>array('class'=>'span8 m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Employee Type'))
                ),
                'empty_value' => '---Select Employee Type---',
                'choices' => array(
                    'Full Time' => 'Full Time',
                    'Half Time' => 'Half Time',
                    'Contractual' => 'Contractual',
                    'Hourly' => 'Hourly'
                ),
            ))*/
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter any remark')))

        ;


    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HumanResourceBundle\Entity\Payroll'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'employee';
    }
}
