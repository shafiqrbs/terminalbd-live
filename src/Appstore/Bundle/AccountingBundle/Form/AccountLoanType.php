<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\globalOption;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountLoanType extends AbstractType
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

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span6 numeric','placeholder'=>'Amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter amount'))
                )))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add Remark')))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Transaction Method'))
                ),
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id");
                }
            ))
            /*->add('customer','text', array('attr'=>array('class'=>'m-wrap span11 select2Customer leftMargin','placeholder'=>'Select customer name','focus' => true),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Search the customer name/mobile'))
            )))*/
            ->add('employee', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'empty_value' => '---Choose a employee---',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->join('b.profile','p')
                        ->where("b.globalOption =".$this->globalOption->getId())
                        ->andWhere("p.name != 'NULL'");
                },
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
            ->add('transactionType', 'choice', array(
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'expanded'      =>false,
		        'multiple'      =>false,
		        'choices' => array(
			        'Debit' => 'Receive',
			        'Credit' => 'Payment'
		        ),
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
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountLoan'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'account';
    }

    public function getSalesInvoice()
    {
        return null;
    }


}
