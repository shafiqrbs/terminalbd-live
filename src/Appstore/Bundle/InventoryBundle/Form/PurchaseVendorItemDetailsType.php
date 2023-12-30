<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseVendorItemDetailsType extends AbstractType
{

    /** @var InventoryConfig */

    public  $inventoryConfig;

    function __construct(InventoryConfig $inventoryConfig)
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','readonly'=>'readonly', 'placeholder'=>'vendor base item name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add vendor base item name'))
                )))
            ->add('webName','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'web base item name')))
            ->add('webPrice','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'web price')))
            ->add('content','textarea', array('attr'=>array('class'=>'no-resize span12','rows'=>5)))
            ->add('masterItem', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Product',
                'empty_value' => '---Choose a item ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2', 'readonly'=>'readonly'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");

                },
            ))
            ->add('brand', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemBrand',
                'empty_value' => '---Choose a brand ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");

                },
            ))
            ->add('warningText','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('warningLabel', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a warning label---',
                'choices' => array(
                    'Warranty' => 'Warranty',
                    'Guarantee' => 'Guarantee',
                ),
            ))
            ->add('country', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\LocationBundle\Entity\Country',
                'empty_value' => '---Choose a country ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy("p.name","ASC");

                },
            ))

            ->add('ageGroup', 'choice', array(
                'attr'=>array('class'=>'span6 select2'),
                'choices' => array(
                    'Kids' => 'Kids',
                    'Adult' => 'Adult'
                ),
            ))

            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'choices' => array(
                    'Male' => 'Male',
                    'Female' => 'Female'
                ),
            ))
            ->add('file')
            ->add('isWeb')

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_purchasevendoritem';
    }
}
