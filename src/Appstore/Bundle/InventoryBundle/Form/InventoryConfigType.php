<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InventoryConfigType extends AbstractType
{



    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('vatRegNo','text', array('attr'=>array('class'=>'m-wrap span10 ','placeholder'=>'Registration no.')))
            ->add('salesReturnDayLimit','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('shopName','text',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Barcode code print shop name')))
            ->add('invoiceNote','text',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'POS print invoice note')))
            ->add('vatPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span10')))
            ->add('removeImage')
            ->add('autoPayment')
            ->add('androidSales')
            ->add('emptySales')
            ->add('showSalesPrice')
            ->add('file')
            ->add('usingBarcode',
                'choice', array(
                'attr'=>array('class'=>'m-wrap  inline-radio'),
                'choices' => array(
                    'item'  => 'Stock Item',
                    'purchase-item'   => 'Purchase-item',
                ),
                'required'    => true,
                'multiple'    => false,
                'expanded'  => true,
                'empty_data'  => "item",
            ))
            ->add('vatMode',
                'choice', array(
                'attr'=>array('class'=>'m-wrap  inline-radio'),
                'choices' => array(
                    'including'  => 'Including',
                    'excluding'   => 'Excluding',
                ),
                'required'    => true,
                'multiple'    => false,
                'expanded'  => true,
                'empty_data'  => "including",
            ))
            ->add('printer',
                'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array(
                    'save'          => 'Save',
                    'printer'       => 'Printer',
                    'pos'           => 'Pos Printer',
                ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => null,
            ))
            ->add('barcodePriceHide')
            ->add('isPos')
            ->add('cartImage')
            ->add('customPrint')
            ->add('vatEnable')
            ->add('systemReset')
            ->add('isAttribute')
            ->add('barcodeItem')
            ->add('isInvoice')
            ->add('isColor')
            ->add('isSize')
            ->add('isCategory')
            ->add('isModel')
            ->add('barcodeColor')
            ->add('barcodePrint')
            ->add('barcodeSize')
            ->add('isPrintFooter')
            ->add('invoicePrintLogo')
            ->add('barcodeBrandVendor',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '0' => '--Select one--',
                        '1' => 'Brand',
                        '2' => 'Vendor',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => null,
            ))
            ->add('barcodeThickness',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '20' => '20',
                        '22' => '22',
                        '24' => '24',
                        '26' => '26',
                        '28' => '28',
                        '30' => '30',
                        '32' => '32',
                        '34' => '34',
                        '36' => '36',
                        '38' => '38',
                        '40' => '40',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => 30,
            ))
            ->add('barcodeFontSize',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '7' => '7',
                        '8' => '8',
                        '9' => '9',
                        '10' => '10',
                        '11' => '11',
                        '12' => '12',
                        '13' => '13',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => 8,
            ))
            ->add('salesMode',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap inline-radio'),
                    'choices' => array(
                        'stock' => 'Stock',
                        'purchase-item' => 'Purchase Item'
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => true,
                    'empty_data'  =>"stock",
                ))
            ->add('deliveryProcess',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap inline-radio'),
                    'choices' => array(
                        'manual-sales' => 'Simple sales',
                        'general-sales' => 'General Sales'
                    ),
                    'empty_value' => '---Choose a sales ---',
                    'required'    => false,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  =>"manual-sales",
                ))
            ->add('barcodeScale',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  =>1,
            ))
            ->add('barcodePerRow',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                         1 => 1,
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                        '6' => '6',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  =>1,
            ))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('address','textarea',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>"Enter shop address"),'required'=>true))
            ->add('printFooterText','textarea',array('attr'=>array('class'=>'m-wrap span12','placeholder'=>"Enter print footer text"),'required'=>true))
            ->add('barcodeText',
                'choice', array(
                    'attr'=>array('class'=>'m-wrap span12'),
                    'choices' => array(
                        '' => '--Select Barcode Text--',
                        'including vat' => 'including vat',
                        'without vat' => 'without vat',
                        '+ vat' => '+ vat',
                    ),
                    'required'    => true,
                    'multiple'    => false,
                    'expanded'  => false,
                    'empty_data'  => null,
            ))
            ->add('currency', 'entity', array(
                'required'    => true,
                'class' => "Setting\Bundle\ToolBundle\Entity\Currency",
                'empty_value' => '---Choose a currency ---',
                'property' => 'nameWithCode',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->orderBy("e.id","ASC");
                },
            ))
            ->add('barcodeHeight','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('barcodeWidth','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('barcodeMargin','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('barcodePadding','integer',array('attr'=>array('class'=>'m-wrap numeric span8')))
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\InventoryConfig'
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
