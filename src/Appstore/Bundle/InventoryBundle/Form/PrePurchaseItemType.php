<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrePurchaseItemType extends AbstractType
{

    public  $inventoryConfig;

    public function __construct(InventoryConfig $inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('masterItem', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Product',
                'empty_value' => '---Choose a item ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2 itemInput'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");
                },
            ))

            ->add('requestQnt','text', array('attr'=>array('class'=>'itemInput m-wrap span8','placeholder'=>'Item quantity'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add item quantity')))))

            ->add('requestBy','text', array('attr'=>array('class'=>'itemInput m-wrap span12','placeholder'=>'Item sales price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Add sales price')))))

            ->add('availableDate', 'date', array(
                'widget' => 'single_text',
                'placeholder' => array(
                    'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

                ),
                'format' => 'dd-MM-yyyy',
                'attr' => array('class'=>'m-wrap span12 datePicker purchaseInput'),
                'view_timezone' => 'Asia/Dhaka'))
            ->add('content','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter remark')))
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\PrePurchaseItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'prePurchaseItem';
    }
}
