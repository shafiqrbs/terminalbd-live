<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseType extends AbstractType
{

    public  $inventoryConfig;

    public function __construct(InventoryConfig $inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'vendorWithCode',
                'attr'=>array('class'=>'span12 select2 m-wrap purchaseInput'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->where("wt.status = 1")
                        ->andWhere("wt.inventoryConfig =".$this->inventoryConfig->getId());
                },
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'empty_value' => '---Choose payment method---',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod purchaseInput'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap purchaseInput'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->inventoryConfig->getGlobalOption()->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('accountMobileBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile banking---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap purchaseInput'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->inventoryConfig->getGlobalOption()->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span12 ','required' => true ,'label' => 'form.name','placeholder'=>'Memo no')))

            ->add('receiveDate', 'date', array(
                'attr' => array('class'=>'m-wrap span12 purchaseInput'),
                'view_timezone' => 'Asia/Dhaka'))

            ->add('paymentAmount','text', array('attr'=>array('class'=>'purchaseInput m-wrap span12 numeric','placeholder'=>'Net payment')))
            /*
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'No of Qnt'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total qnt'))
                )))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'No of item'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total item'))
                )))
            ->add('purchaseTo', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'m-wrap span12 purchaseInput'),
                'empty_value' => '---Choose a purchase To ---',
                'choices' => array(
                    'International' => 'International',
                    'National' => 'National',
                    'In House' => 'In House'
                ),
            ))*/
            ->add('process', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'purchaseInput m-wrap span12'),
                'choices' => array(
                    'created' => 'Created',
                    'complete' => 'Complete',
                    'imported' => 'Imported',
                    'approved' => 'Approved',
                ),
            ))
          /*  ->add('asInvestment')*/
          /*  ->add('file','file',array('attr'=>array('class'=>'m-wrap span3')))*/

            /*->add('advanceAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            /*->add('advanceAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('commissionAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('vatAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('taxAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentType','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentMethod','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))*/
            /*->add('file','file',array('attr'=>array('class'=>'default span12')))*/
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\Purchase'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchase';
    }
}
