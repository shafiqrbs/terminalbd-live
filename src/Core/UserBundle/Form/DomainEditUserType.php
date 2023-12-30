<?php

namespace Core\UserBundle\Form;


use Core\UserBundle\Entity\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Repository\DesignationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class DomainEditUserType extends AbstractType
{

    /** @var  GlobalOption */
    private $globalOption;

    /** @var  DesignationRepository */
    private $designation;

    /** @var  UserRepository */
    private $user;


    function __construct(UserRepository $user , GlobalOption $globalOption,DesignationRepository $designation)
    {
        $this->user = $user;
        $this->globalOption = $globalOption;
        $this->designation = $designation;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter your valid email address'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter your email address')),
                        new Length(array('max'=>200))
                    ))
            )
            ->add('roles', 'choice', array(
                    'attr'=>array('class'=>'category form-control'),
                    'required'=>true,
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required'))
                    ),
                    'multiple'    => true,
                    'empty_data'  => null,
                    'choices'   => $this->getAccessRoleGroup())
            )
            ->add('appRoles', 'choice', array(
                    'attr'=>array('class'=>'category form-control'),
                    'required'=>false,
                    'multiple'    => true,
                    'empty_data'  => null,
                    'choices'   => $this->getAppRoleGroup())
            )
            ->add('enabled');
            $builder->add('profile', new DomainUserProfileType($this->globalOption, $this->designation));

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
        return 'Core_userbundle_user';
    }

    public function getAccessRoleGroup(){

        return $userGroups =$this->user->getAccessRoleGroup($this->globalOption);
    }
    public function getAppRoleGroup(){
        return $userGroups = $this->user->getAndroidRoleGroup();
    }
}