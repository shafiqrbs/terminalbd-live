<?php

namespace Core\UserBundle\Form;

use Doctrine\ORM\EntityRepository;

use Core\UserBundle\Form\Type\ProfileFormType;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Form\GlobalOptionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Syndicate\Bundle\ComponentBundle\Form\EducationType;
use Syndicate\Bundle\ComponentBundle\Form\VendorType;

class UserType extends BaseType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       // var_dump($options['data']->getId());
        $userID = (!empty($options['data'])) ? $options['data']->getId():0;
        $builder
        ->add('name','text', array('attr'=>array('class'=>'m-wrap placeholder-no-fix','placeholder'=>'Enter your full name'),
        'constraints' =>array(
            new NotBlank(array('message'=>'Please input required')),
            new Length(array('max'=>200))
        )))
        ->add('email','email', array('attr'=>array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle' , 'class'=>'m-wrap placeholder-no-fix','placeholder'=>'Enter your email address' , 'autocomplete' => 'off'),
                                        'constraints' =>array(
                                            new NotBlank(array('message'=>'Please input required')),
                                            new Length(array('max'=>200))
                                        )));

        if($userID == 0){
            $builder->add('username','text', array('attr'=>array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle' , 'class'=>'m-wrap placeholder-no-fix','placeholder'=>'Enter your user name', 'autocomplete' => 'off'),
                                                   'constraints' =>array(
                                                       new NotBlank(array('message'=>'Please input required')),
                                                       new Length(array('max'=>200))
                                                   )))
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch'
                ));
            $builder->add('globalOption', new GlobalOptionType());
        }

	    $builder->add('profile', new ProfileFormType($userID > 0));


        if($userID > 0) {

            $builder->add('groups', 'entity', array(
                'class' => 'Core\UserBundle\Entity\Group',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->andWhere("g.name != :group")
                        ->setParameter('group', 'Super Administrator');
                },
                'attr'     =>array('id' => 'select2_sample4' , 'class' => 'span12 select2'),
                'property' => 'name',
                'multiple' => true,
            ));
        }

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
}