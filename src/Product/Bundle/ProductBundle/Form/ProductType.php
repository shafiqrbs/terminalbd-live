<?php

namespace Product\Bundle\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter item name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('vendorPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter vendor price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter regular price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('shipping','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter regular price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('brand', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Branding',
                'empty_value' => '---Select brand name---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span8 selectbox'),
                'query_builder' => function(BrandingRepository $er){
                        return $er->createQueryBuilder('c')
                            ->andWhere("c.status = 1")
                            ->orderBy('c.name','ASC');
                    }
            ))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter sales price')))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter number of quantity')))
            ->add('size','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','data-trigger'=>'hover' , 'data-original-title'=> 'Size ie:S|M|XL/30|34|34', 'placeholder'=>'Enter size ie:S|M|XL/30|34|34')))
            ->add('weight','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','data-trigger'=>'hover' , 'data-original-title'=> 'Product weight Kg,Pound','placeholder'=>'Enter product weight')))
            ->add('color','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','data-trigger'=>'hover' , 'data-original-title'=> 'Color ie: Red|Green|Blue|White', 'placeholder'=>'Enter color ie: Red|Green|Blue')))
            ->add('madeIn','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','data-trigger'=>'hover' , 'data-original-title'=> 'Product made in ie: Bangladesh,China,Japan,USA', 'placeholder'=>'Product made in ie: Bangladesh,China,Japan,USA')))
            ->add('dimension','text', array('attr'=>array('class'=>'m-wrap span12  tooltips','data-trigger'=>'hover' , 'data-original-title'=> 'Product dimension ie: 200x100x50', 'placeholder'=>'Enter Product dimension ie: 200x100x50')))
            ->add('file')
            ->add('isFeature')
            ->add('content','textarea', array('attr'=>array('class'=>'no-resize span12','rows'=>5)))
            ->add('status', 'choice', array('choices' => array('1' => 'Publish', '2' => 'Pending', '3' => 'Expired'),
                'expanded'=>true)
            )
            ->add('availability', 'choice', array(
                    'attr'=>array('class'=>'selectbox span8'),
                    'choices' => array(
                    'In stock' => 'In stock',
                    'Pre-order' => 'Pre-order',
                    'Confirm sms' => 'Confirm sms',
                    'Out of stock' => 'Out of stock'
                ),
                'expanded'=>false)
            )
            ->add('dealType', 'choice', array(
                    'attr'=>array('class'=>'selectbox span8'),
                    'choices' => array(
                    '' => '--select deal--',
                    'Today' => 'Today',
                    'Weekly' => 'Weekly',
                    'Monthly' => 'Monthly'
                ),
                    'expanded'=>false)
            )
            ->add('unit', 'choice', array(
                    'attr'=>array('class'=>'selectbox span8'),
                    'choices' => array(
                    'Bag' => 'Bag',
                    'Bottle' => 'Bottle',
                    'Box' => 'Box',
                    'Can' => 'Can',
                    'Cft' => 'Cft',
                    'Coil' => 'Coil',
                    'Cylinder' => 'Cylinder',
                    'Carton' => 'Carton',
                    'Feet' => 'Feet',
                    'Gallon' => 'Gallon',
                    'Jar' => 'Jar',
                    'Job' => 'Job',
                    'Kg' => 'Kg',
                    'Liter' => 'Liter',
                    'Meter' => 'Meter',
                    'ML' => 'ML',
                    'MM' => 'MM',
                    'Nos' => 'Nos',
                    'Pail' => 'Pail',
                    'Pair' => 'Pair',
                    'Pcs' => 'Pcs',
                    'Packet' => 'Packet',
                    'Pound' => 'Pound',
                    'Prs' => 'Prs',
                    'Refile' => 'Refile',
                    'Rft' => 'Rft',
                    'Rim' => 'Rim',
                    'Roll' => 'Roll',
                    'Set' => 'Set',
                    'Sft' => 'Sft',
                    'Yard' => 'Yard',
                ),
                'expanded'=>false)
            );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Product\Bundle\ProductBundle\Entity\Product'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product_bundle_productbundle_product';
    }

    public function getGroupCollection()
    {

    }
}
