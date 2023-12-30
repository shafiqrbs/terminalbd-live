<?php

namespace Appstore\Bundle\HumanResourceBundle\Form;


use Core\UserBundle\Entity\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Repository\DesignationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserType extends AbstractType
{


    /** @var  UserRepository */
    private $user;

    /** @var  GlobalOption */
    private $option;

    /** @var  LocationRepository */
    private $location;


     /** @var  DesignationRepository */
    private $designationRepository;


    function __construct( UserRepository $user , GlobalOption $option , LocationRepository $location, DesignationRepository $designationRepository)
    {
        $this->location = $location;
        $this->user = $user;
        $this->option = $option;
        $this->designationRepository = $designationRepository;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userGroup', 'choice', array(
        'attr'=>array('class'=>'m-wrap span8'),
        'expanded'      => false,
        'multiple'      => false,
        'choices' => array(
            'employee' => 'Employee',
            'user' => 'User',
            'marketing' => 'Marketing Executive',
        ),
        ));
        $builder->add('profile', new \Appstore\Bundle\DomainUserBundle\Form\EmployeeProfileType($this->user, $this->option,$this->location));
      //  $builder->add('employeePayroll', new EmployeePayrollType($this->user, $this->option,$this->location));
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
