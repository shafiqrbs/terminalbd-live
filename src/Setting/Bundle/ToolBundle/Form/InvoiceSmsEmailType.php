<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceSmsEmailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentMobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add payment mobile no','data-original-title'=>'Add payment mobile no','autocomplete'=>'off')))
            ->add('transaction','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment transaction id','data-original-title'=>'Add payment transaction id','autocomplete'=>'off')))
            ->add('accountNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add account no','data-original-title'=>'Add payment transaction id','autocomplete'=>'off')))

            ->add('transactionMethod', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select transaction method'))
                ),
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'empty_value' => '---Choose mobile bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->orderBy("b.name", "ASC");
                }
            ))

            ->add('portalBankAccount', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\PortalBankAccount',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'empty_value' => '---Choose mobile bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->orderBy("b.name", "ASC");
                }
            ))
            ->add('portalMobileBankAccount', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\PortalMobileBankAccount',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'empty_value' => '---Choose mobile bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->orderBy("b.name", "ASC");
                }
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_invoicesmsemail';
    }
}
