<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountBankType extends AbstractType
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

            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add bank address')))

            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add account no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
            ))
            ->add('accountOwner','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add account ownership'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
            ))
            ->add('branch','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'add branch name'),
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
                    'Savings' => 'Savings',
                    'Current' => 'Current',
                    'Other' => 'Other'
                ),
            ))
            ->add('bank', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Bank',
                'empty_value' => '---Choose a bank---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->orderBy("b.name", "ASC");
                },
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank'
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
