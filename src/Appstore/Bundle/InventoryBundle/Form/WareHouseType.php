<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WareHouseType extends AbstractType
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
            ->add('name')
            ->add('parent', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\WareHouse',
                'empty_value' => '---Select warehouse---',
                'property' => 'name',
                'attr'=>array('class'=>'selectbox'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('w')
                        ->where("w.status = 1")
                        ->andWhere("w.isWareHouse = 1")
                        ->andWhere("w.inventoryConfig =". $this->inventoryConfig->getId())
                        ->orderBy('w.name','ASC');
                },
            ))
            ->add('isWareHouse')
            ->add('status')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\WareHouse'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_warehouse';
    }
}
