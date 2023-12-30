<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\Damage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryReturnType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('purchaseItem','text', array('attr'=>array('class'=>'m-wrap span12'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  size name'))
            )))
            ->add('quantity','integer', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Quantity'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  quantity'))
            )))
            ->add('notes','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Notes...')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_delivery_return';
    }
}
