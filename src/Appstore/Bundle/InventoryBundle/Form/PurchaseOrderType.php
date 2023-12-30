<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseOrderType extends AbstractType
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
                'required'    => false,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->andWhere("wt.status = 1")
                        ;
                },
            ))

            ->add('invoice','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'invoice no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  invoice no'))
                )
            ))
            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'memo no')))
            /*->add('chalan','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))*/
            ->add('receiveDate','date', array('attr'=>array('class'=>'m-wrap span12 datepicker','placeholder'=>''),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )))
            ->add('totalAmount','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'total amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total amount BDT'))
                )))
            ->add('paymentAmount','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'payment amount BDT'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))
            ->add('dueAmount','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'due amount BDT')))
            ->add('commissionAmount','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'comission amount BDT')))
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'total Qnt'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add total qnt'))
                )))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'total item')))
            /*->add('advanceAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('commissionAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('vatAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('taxAmount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalQnt','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('totalItem','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentType','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
            ->add('paymentMethod','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))*/
            ->add('file','file',array('attr'=>array('class'=>'default span12')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\PurchaseOrder'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_purchaseorder';
    }
}
