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


class DomainSignType extends AbstractType
{


    /** @var  GlobalOption */
    private $globalOption;

    /** @var  LocationRepository */
    private $location;

    /** @var  DesignationRepository */
    private $designation;

    /** @var  UserRepository */
    private $user;



    function __construct(UserRepository $user , GlobalOption $globalOption, LocationRepository $location , DesignationRepository $designation)
    {
        $this->user = $user;
        $this->globalOption = $globalOption;
        $this->location = $location;
        $this->designation = $designation;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder


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

            ->add('username','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off' ,'placeholder'=>'Enter your user name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter your user name')),
                        new Length(array('max'=>200))
                    ))
            )
            ->add('enabled')
            ->add('plainPassword', 'repeated', array(
                'attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off'),
                'type' => 'password',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter user password'))),
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter your valid email address'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter user email address')),
                        new Length(array('max'=>200))
            ))
            );
            $builder->add('profile', new DomainProfileType($this->globalOption , $this->location , $this->designation));

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

    public function getAccessRoleGroup(){

        return $userGroups = $this->user->getAccessRoleGroup($this->globalOption);
    }
    public function getAppRoleGroup(){

        return $userGroups = $this->user->getAndroidRoleGroup();
    }
}