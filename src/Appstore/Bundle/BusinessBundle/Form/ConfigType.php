<?php

namespace Appstore\Bundle\BusinessBundle\Form;


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

            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 3,'placeholder'=>'Enter company address')))
            ->add('printFooterText','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 3,'placeholder'=>'Enter print footer')))
            ->add('invoiceComment','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 3,'placeholder'=>'Enter invoice comment')))
            ->add('bodyFontSize', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('' => 'Font Size','10px' => '10px',  '11px' => '11px','12px' => '12px','13px' => '13px','14px' => '14px', '15px' => '15px', '16px' => '16px', '17px' => '17px','18px' => '18px',  '20px' => '20px', '22px' => '22px','24px' => '24px', '26px' => '26px',  '28px' => '28px','30px' => '39px','32px' => '32px','34px' => '34px','36px' => '36px', '38px' => '38px', '40px' => '40px','42px' => '42px',  '44px' => '44px', '46px' => '46px','48px' => '48px'),
            ))
            ->add('productionType', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('' => '-- Select Production --', 'pre-production' => 'Pre Production','post-production' => 'Post Production','vendor-stock' => 'Vendor Stock'),
            ))
	        ->add('businessModel', 'choice', array(
		        'attr'=>array('class'=>'m-wrap span12'),
		        'choices' => array(
			        '' => '-- Select business model --',
			        'general' => 'General',
			        'distribution' => 'Distribution',
			        'sign' => 'Digital-Sign',
			        'electrical' => 'Electrical',
			        'stationary' => 'Stationary',
			        'commission' => 'Agent Commission',
			        'event' => 'Event',
			        'bricks' => 'Bricks',
			        'sawmill' => 'Sawmill',
			        'association' => 'Association',
		        ),
	        ))
	        ->add('stockFormat',
		        'choice', array(
			        'attr'=>array('class'=>'m-wrap  span12'),
			        'choices' => array(
				        'wearhouse'           => 'Wearhouse',
				        'category'           => 'Category'
			        ),
			        'required'    => false,
			        'multiple'    => true,
			        'expanded'  => false,
			        'empty_data'  => null,
		        ))
            ->add('borderColor','text', array('attr'=>array(
                'class'=>'m-wrap span9 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('borderWidth','text',array('attr'=>array('class'=>'m-wrap numeric span8','maxLength'=>2)))
            ->add('fuelStation')
            ->add('isPowered')
            ->add('isUnitPrice')
	        ->add('invoicePrintLogo')
            ->add('customInvoicePrint')
            ->add('customInvoice')
            ->add('bonusFromStock')
            ->add('isMarketingExecutive')
            ->add('isInvoiceTitle')
            ->add('showStock')
            ->add('isPrintHeader')
            ->add('isDescription')
            ->add('posPrint')
            ->add('isPrintFooter')
            ->add('isStockHistory')
            ->add('srCommission')
            ->add('tloCommission')
            ->add('salesReturn')
            ->add('storeLedger')
            ->add('zeroStock')
            ->add('conditionSales')
            ->add('printOutstanding')
            ->add('removeImage')
            ->add('file')
            ->add('vatPercent','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('aitPercent','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('invoiceWidth','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'config';
    }



}
