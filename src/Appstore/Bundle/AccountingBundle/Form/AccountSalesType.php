<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\globalOption;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountSalesType extends AbstractType
{

    public  $globalOption;

    public function __construct(\Setting\Bundle\ToolBundle\Entity\GlobalOption $globalOption)
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

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span4 numeric removeZero','placeholder'=>'Received amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add payment amount'))
                )))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remark')))
            ->add('customer','text', array('attr'=>array('class'=>'m-wrap span9 select2Customer leftMargin','placeholder'=>'Select customer name','focus' => true)))
            ->add('smsAlert',CheckboxType::class, array('attr'=>array('class'=>'')))
            ->add('processHead', 'choice', array(
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'expanded'      =>false,
		        'multiple'      =>false,
		        'choices' => array(
                    'Due' => 'Receive from Due',
                    'Discount' => 'Discount',
                    'Advance' => 'Receive of Advance',
                    'Debit' => 'Debit Adjustment(Receive)',
                    'Credit' => 'Credit Adjustment(Payment)',
		        ),
	        ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'empty_value' => '---Choose a Transaction Method---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id");
                }
            ))
            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.globalOption =".$this->globalOption->getId())
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
                        ->where("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountSales'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'accountSales';
    }

    public function getSalesInvoice()
    {
        return null;
    }


}
