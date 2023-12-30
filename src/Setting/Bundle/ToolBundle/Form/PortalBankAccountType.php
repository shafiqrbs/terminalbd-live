<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PortalBankAccountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bank', 'entity', array(
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'empty_value' => '---Select Bank ---',
                'property' => 'name',
                'attr'=>array('class'=>'select2 span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->orderBy('wt.name','ASC');
                },
            ))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter contact person'))
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter mobile no'))
                )
            ))
            ->add('branch','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter branch name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter branch name'))
                )
            ))
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter account no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter account no'))
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\PortalBankAccount'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_portalbankaccount';
    }
}
