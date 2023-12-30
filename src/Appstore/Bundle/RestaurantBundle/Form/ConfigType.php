<?php

namespace Appstore\Bundle\RestaurantBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ConfigType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('vatRegNo','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter vat registration no')))
            ->add('vatPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span5','max'=> 100)))
            ->add('address','textarea',array('attr'=>array('class'=>'m-wrap span11','rows'=>5,'placeholder'=> "Enter office/store address")))
            ->add('invoiceNote','textarea',array('attr'=>array('class'=>'m-wrap span11','rows' => 3,'placeholder'=> "Enter invoice notes")))
            ->add('printFooterText','textarea',array('attr'=>array('class'=>'m-wrap span11','rows' => 3,'placeholder'=> "Enter print footer text")))
            ->add('autoPayment')
            ->add('isCustomer')
            ->add('vatEnable')
            ->add('tablePlan')
            ->add('productImage')
            ->add('additionalTable')
            ->add('additionalServe')
            ->add('posPrint')
            ->add('deliveryPrint')
            ->add('invoicePrintLogo')
            ->add('isStockHistory')
            ->add('isInvoiceTitle')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('isPrintFooter')
            ->add('printInstruction')
            ->add('kitchenPrint')
            ->add('printToken')
            ->add('isProduction')
            ->add('vatMode', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---VAT Mode---',
                'choices' => array(
                    'including' => 'Including',
                    'excluding' => 'Excluding',
                ),
            ))
            ->add('discountType', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Discount Type---',
                'choices' => array(
                    'percentage' => 'Percentage',
                    'flat' => 'Flat',
                ),
            ))
             ->add('salesMode', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Sales Mode---',
                'choices' => array(
                    'category' => 'Category',
                    'table' => 'Table',
                ),
            ))
            ->add('payFor', 'choice', array(
                'required'    => true,
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array(
                    'pre-pay' => 'Pre-pay',
                    'post-pay' => 'Post-pay',
                ),
            ))
            ->add('discountPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span5')))
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('templateCss','textarea',array('attr'=>array('class'=>'m-wrap span12','rows'=>10)))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restaurant_config';
    }


}
