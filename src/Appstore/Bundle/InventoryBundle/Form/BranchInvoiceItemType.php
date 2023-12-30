<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BranchInvoiceItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('barcode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter barcode'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter barcode'))
            )))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add  quantity')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\BranchInvoiceItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_branchInvoiceItem';
    }
}
