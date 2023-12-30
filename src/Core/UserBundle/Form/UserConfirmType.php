<?php

namespace Core\UserBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserConfirmType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username','text', array('attr'=>array('class'=>'mobile','placeholder'=>'Enter your mobile no'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter your mobile no')),
                        new Length(array('max'=>200))
                    ))
            )
            ->add('password','text', array('attr'=>array('class'=>'secured','placeholder'=>'Enter your secured no on your mobile '),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter your secured password')),
                        new Length(array('max'=>200))
                    ))
            );

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