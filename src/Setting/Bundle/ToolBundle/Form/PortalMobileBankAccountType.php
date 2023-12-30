<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PortalMobileBankAccountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 numeric mobile','placeholder'=>'add payment receive mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'add payment receive mobile no'))
                )))
            ->add('authorised','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add authorised company'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
            ))
            ->add('serviceName', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'choices' => array(
                    'Bkash' => 'Bkash',
                    'Rocket' => 'Rocket',
                ),
            ))
            ->add('accountOwner','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add account ownership'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
            ))
            ->add('accountType', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'choices' => array(
                    'Personal' => 'Personal',
                    'General' => 'General',
                    'Merchant' => 'Merchant',
                ),
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\PortalMobileBankAccount'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_portalmobilebankaccount';
    }
}
