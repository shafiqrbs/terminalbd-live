<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


class SignupProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
           $builder
                ->add('name','text',
                    array('required' => true,'mapped'=>false,'attr'=>array('class'=>'m-wrap span12','mapped'=>false,'placeholder'=>'Enter your full name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter your full name required')),
                    new Length(array('max'=>200))
                    ))
                )
                ->add('username','text', array('required' => true,'mapped' => false,'attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your full name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter user name required')),
                    new Length(array('max'=>200))
                    ))
                )
                ->add('mobile','text', array('attr'=>array('class'=>'m-wrap  mobile span12','placeholder'=>'Enter your mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter your mobile no required')),
                    new Length(array('max'=>200))
                    ))
                )

           ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\Profile'
        ));
    }

    public function getName()
    {
        return 'manage_profile';
    }
}
