<?php

namespace Appstore\Bundle\HumanResourceBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\EmployeeProfileType;
use Core\UserBundle\Entity\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Repository\DesignationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{


    /** @var  UserRepository */
    private $user;

    /** @var  GlobalOption */
    private $option;

    /** @var  LocationRepository */
    private $location;


    function __construct( UserRepository $user , GlobalOption $option , LocationRepository $location)
    {
        $this->location = $location;
        $this->user = $user;
        $this->option = $option;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Customer name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter customer name'))
                    ))
            )
            ->add('email','email', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Mobile no'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter email address'))
                    ))
            )
            ->add('enabled')
            ->add('userGroup', 'choice', array(
                'attr'=>array('class'=>'m-wrap span8'),
                'expanded'      => false,
                'multiple'      => false,
                'choices' => array(
                    'employee' => 'Employee',
                    'user' => 'User',
                    'marketing' => 'Marketing Executive',
                ),
            ))
            ->add('roles', 'choice', array(
                    'attr'=>array('class'=>'category form-control'),
                    'required' => false,
                    'multiple'    => true,
                    'empty_data'  => null,
                    'choices'   => $this->getAccessRoleGroup())
            )
        ;
        $builder->add('profile', new EmployeeProfileType($this->user, $this->option,$this->location));
        $builder->add('employeePayroll', new EmployeePayrollType($this->user, $this->option,$this->location));

    }

    public function getAccessRoleGroup(){

        return $userGroups = $this->user->getAccessRoleGroup($this->option);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }


}
