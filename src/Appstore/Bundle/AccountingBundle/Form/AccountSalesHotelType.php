<?php

namespace Appstore\Bundle\AccountingBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountSalesHotelType extends AbstractType
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

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Add receive amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add receive amount'))
                )))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add Remark')))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id");
                }
            ))
            ->add('customer', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer',
                'empty_value' => '---Choose a customer---',
                'property' => 'nameMobile',
                'attr'=>array('class'=>'span12 select2 customer-ledger'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->globalOption->getId());
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
            ->add('processHead', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'Due' => 'Due',
                    'Advance' => 'Advance',
                    'Outstanding' => 'Outstanding',
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
