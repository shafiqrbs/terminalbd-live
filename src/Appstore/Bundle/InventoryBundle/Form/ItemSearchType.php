<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping;
use Appstore\Bundle\InventoryBundle\Repository\ItemTypeGroupingRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotNull;

class ItemSearchType extends AbstractType
{

    /** @var  ItemTypeGroupingRepository */

    private $em;

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
            ->add('masterItem', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Product',
                'empty_value' => '---Choose a item ---',
                'property' => 'name',
                'attr'=>array('class'=>'span3 select2Product'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.inventoryConfig =".$this->inventoryConfig->getId())
                        ->orderBy("p.name","ASC");

                },
            ))
            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span3 select2Vendor'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('v')
                        ->where("v.status = 1")
                        ->andWhere("v.inventoryConfig =".$this->inventoryConfig->getId());

                },
            ))
            ->add('color', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemColor',
                'empty_value' => '---Choose a color ---',
                'property' => 'name',
                'attr'=>array('class'=>'span3 select2Color'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('v')
                        ->where("v.status = 1")
                        ->andWhere("v.inventoryConfig =".$this->inventoryConfig->getId());

                },
            ))
            ->add('size', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemSize',
                'empty_value' => '---Choose a size ---',
                'property' => 'name',
                'attr'=>array('class'=>'span3 select2Size'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('v')
                        ->where("v.status = 1")
                        ->andWhere("v.inventoryConfig =".$this->inventoryConfig->getId());

                },
            ))
          ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_item';
    }

    /**
     * @return mixed
     */
    protected function ItemTypeChoiceList()
    {
        return $itemTypeTree = $this->em->getItemTypeTree();

    }
}
