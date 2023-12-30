<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountMobileBankType extends AbstractType
{

    public  $globalOption;

    public function __construct(GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;

    }

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
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    'Bkash'         => 'Bkash',
                    'Rocket'        => 'Rocket',
                    'Nexus'         => 'Nexus',
                    'iPay'          => 'iPay',
                    'Nagad'         => 'Nagad',
                    'Ucash'         => 'Ucash',
                    'City Touch'    => 'City Touch',
                ),
            ))
            ->add('accountOwner','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add account ownership'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
            ))
            ->add('serviceCharge','text', array('attr'=>array('class'=>'m-wrap span12','maxLength' => 5 ,'placeholder'=>'Service charge')
            ,'constraints' =>array(new NotBlank(array('message'=>'Please input required'))),
            ))
            ->add('accountType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    'Personal' => 'Personal',
                    'General' => 'General',
                    'Merchant' => 'Merchant',
                ),
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingbundle_AccountBank';
    }


}
