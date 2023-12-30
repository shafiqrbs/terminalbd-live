<?php

namespace Appstore\Bundle\HumanResourceBundle\Form;


use Appstore\Bundle\DomainUserBundle\Form\EmployeeProfileType;
use Core\UserBundle\Entity\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmployeePayrollType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    /** @var  GlobalOption */
    private $option;

    /** @var  UserRepository */
    private $user;

    function __construct( UserRepository $user , GlobalOption $option , LocationRepository $location)
    {
        $this->location = $location;
        $this->user = $user;
        $this->global = $option;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('effectedMonth','text', array('attr'=>array('class'=>'m-wrap span8 datePickerMonth','placeholder'=>'Enter Effected Month'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input effected month')),
                )
            ))
            ->add('basicAmount','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter basic amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input basic amount')),
                )
            ))
            ->add('bonusApplicable')
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter Account No')))
            ->add('bankAccountName','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter Bank Account Name')))
            ->add('branch','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Bank branch name')))
            ->add('taxNumber','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Tax Number')))

            ->add('bonusPercentage','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter bonus percentage')))
            ->add('bank', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Select Bank Name---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->orderBy("e.name", "ASC");
                }
            ))
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter any remark')))
            ->add('mobileAccount','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter Mobile  Account')))
            ->add('mobileBanking', 'choice', array(
                'attr'=>array('class'=>'m-wrap span8'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Select Mobile Banking---',
                'choices' => array(
                    'Bkash' => 'Bkash',
                    'Rocket' => 'Rocket',
                    'Nagod' => 'Nagod',
                ),
            ))
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
            ->add('paymentMethod', 'choice', array(
                'attr'=>array('class'=>'m-wrap span8'),
                'expanded'      =>false,
                'multiple'      =>false,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please Choose Payment Method')),
                ),
                'empty_value' => '---Choose Payment Method---',
                'choices' => array(
                    'Cash' => 'Cash in Hand',
                    'Bank' => 'Bank',
                    'Mobile Banking' => 'Mobile Banking',

                ),
            ));


    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll'
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
