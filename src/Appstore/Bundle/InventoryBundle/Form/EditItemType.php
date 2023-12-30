<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping;
use Appstore\Bundle\InventoryBundle\Repository\ItemTypeGroupingRepository;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Product\Bundle\ProductBundle\Entity\ItemGroup;
use Product\Bundle\ProductBundle\Entity\ItemGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotNull;

class EditItemType extends AbstractType
{

    /** @var  ItemGroupRepository */
    private $groupRep;

    /** @var  CategoryRepository */
    private $catRep;

    /** @var InventoryConfig */
    public  $inventoryConfig;

    function __construct(InventoryConfig $inventoryConfig, ItemGroupRepository $groupRep, CategoryRepository $catRep )
    {
        $this->inventoryConfig = $inventoryConfig;
        $this->category = $catRep;
        $this->itemGroup = $groupRep;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('name', 'text', array(
                'required'    => true,
                'attr'=>array('class'=>'span12'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('itemUnit', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'property' => 'name',
                'empty_value' => '---Choose a item unit ---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('salesPrice', 'text', array(
                'required'    => false,
                'attr'=>array('class'=>'span12')
            ))
            ->add('purchasePrice', 'text', array(
                'required'    => false,
                'attr'=>array('class'=>'span12')
            ))
            ->add('file');

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
        return 'item';
    }

}
