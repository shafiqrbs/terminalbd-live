<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseItemAttributeType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serialNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Attribute name')))
            ->add('expiredDate', 'date', array(
                'widget' => 'single_text',
                'required' => 'false',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span12 dateCalendar'),
                'view_timezone' => 'Asia/Dhaka'))

            ->add('assuranceFromVendor', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    '1 Month' => '1 Month',
                    '3 Month' => '3 Month',
                    '4 Month' => '4 Month',
                    '6 Month' => '6 Month',
                    '1 Year' => '1 Year',
                    '1 Year 6 Month' => '1 Year 6 Month',
                    '2 Year' => '2 Year',
                    '2 Year 6 Month' => '2 Year 6 Month',
                    '3 Year' => '3 Year',
                    '3 Year 6 Month' => '3 Year 6 Month',
                    '4 Year' => '4 Year',
                    '4 Year 6 Month' => '4 Year 6 Month',
                    '5 Year' => '5 Year',
                    '5 Year 6 Month' => '5 Year 6 Month',
                    'Life Time' => 'Life Time',
                    'Others' => 'Others',

                ),
                'data' => $options->getData() ?: '1 Year'
            ))
            ->add('assuranceToCustomer', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    '1 Month' => '1 Month',
                    '3 Month' => '3 Month',
                    '4 Month' => '4 Month',
                    '6 Month' => '6 Month',
                    '1 Year' => '1 Year',
                    '1 Year 6 Month' => '1 Year 6 Month',
                    '2 Year' => '2 Year',
                    '2 Year 6 Month' => '2 Year 6 Month',
                    '3 Year' => '3 Year',
                    '3 Year 6 Month' => '3 Year 6 Month',
                    '4 Year' => '4 Year',
                    '4 Year 6 Month' => '4 Year 6 Month',
                    '5 Year' => '5 Year',
                    '5 Year 6 Month' => '5 Year 6 Month',
                    'Life Time' => 'Life Time',
                    'Others' => 'Others',
                ),
                'data' => $options->getData() ? : '1 Year'
            ))
            ->add('assuranceType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap'),
                'choices' => array(
                    'Warranty' => 'Warranty',
                    'Grantee' => 'Grantee',
                    'Others' => 'Others',
                ),
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\PurchaseItemAttribute'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_purchaseItemAttribute';
    }


}
