<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class   AccountPurchaseType extends AbstractType
{

    /* @var $global GlobalOption */

    public  $global;

    public function __construct(GlobalOption $global)
    {
        $this->global = $global;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('payment','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'add payment amount removeZero'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount'))

            )))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'empty_value' => '---Choose a transaction method---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id");
                }
            ))
	        ->add('processType', 'choice', array(
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'expanded'      =>false,
		        'multiple'      =>false,
		        'choices' => array(
			        'Due' => 'Payment of Due',
			        'Advance' => 'Payment of Advance',
			        'Debit' => 'Debit Adjustment',
			        'Credit' => 'Credit Adjustment',
		        ),
	        ))
            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->global->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('accountMobileBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile banking---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
	                    ->where("b.status = 1")
	                    ->andWhere("b.globalOption =".$this->global->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'')))
        ;
        if($this->global->getMainApp()->getSlug() == 'miss'){

            $builder->add('medicineVendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineVendor',
                'empty_value' => '---Choose a vendor company---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 m-wrap select2 vendor-ledger-medicine '),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig =".$this->global->getMedicineConfig()->getId())
                        ->orderBy('e.companyName','ASC');
                },
            ));

        }elseif($this->global->getMainApp()->getSlug() == 'inventory'){

            $builder->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor',
                'empty_value' => '---Choose a vendor company---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 select2 m-wrap vendor-ledger-inventory'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.inventoryConfig =".$this->global->getInventoryConfig()->getId())
                        ->orderBy('e.companyName','ASC');
                },
            ));
        }else{
            $builder->add('accountVendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountVendor',
                'empty_value' => '---Choose a vendor company---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span10 select2  vendor-ledger-business'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->global->getId())
                        ->orderBy('e.companyName','ASC');
                },
            ));
        }

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountPurchase'
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
