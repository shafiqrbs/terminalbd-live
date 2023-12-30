<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Product\Bundle\ProductBundle\Entity\ItemGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemType extends AbstractType
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
            ->add('category', 'text', array(
                'required' => true,
                'mapped' => false,
                'attr' => array('class' => 'span12', 'list' => 'categories', 'autocomplete' => 'off'),
                'constraints' => array(
                    new NotBlank(array('message' => 'Please input required'))
                )
            ))
            ->add('inventoryConfig', 'hidden',
                array(
                    'mapped' => false,
                    'attr' => array('data' => $this->inventoryConfig->getId())))
            /*->add('itemGroup', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select item group---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'class' => 'ProductProductBundle:ItemGroup',
                'property' => 'nestedLabel',
                'choices'=> $this->ItemGroupChoiceList()
            ))*/

            ->add('name', 'text', array(
                'required' => true,
                'attr' => array('class' => 'span12', 'list' => 'masterItem', 'autocomplete' => 'off'),
                'constraints' => array(
                    new NotBlank(array('message' => 'Please input required'))
                )
            ))
            ->add('nameBn', 'text', array(
                'required' => false,
                'attr' => array('class' => 'span12', 'list' => 'masterItem', 'autocomplete' => 'off')
            ))
            ->add('itemUnit', 'entity', array(
                'required' => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'property' => 'name',
                'data' => 4,
                'empty_value' => '---Choose a item unit ---',
                'attr' => array('class' => 'span12 select2'),
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name", "ASC");
                },
            ))
            ->add('salesPrice', 'text', array(
                'required' => false,
                'attr' => array('class' => 'span12')
            ))
            ->add('purchasePrice', 'text', array(
                'required' => false,
                'attr' => array('class' => 'span12')
            ))
           /* ->add('file')*/

            ->add('barcode', 'text', array(
            'required'    => false,
            'attr'=>array('class'=>'span12 barcode')
        ));

            if($this->inventoryConfig->getIsVendor() == 1 ){

                $builder
                    ->add('vendor', 'text', array(
                        'required'    => true,
                        'mapped'    => false,
                        'attr'=>array('class'=>'span12','list'=>'vendors','autocomplete'=>'off'),
                        'constraints' =>array(
                            new NotBlank(array('message'=>'Please input required'))
                        )
                    ));

            }
            if($this->inventoryConfig->isModel() == 1 ){

                $builder
                    ->add('model', 'text', array(
                        'required'    => true,
                        'attr'=>array('class'=>'span12 model'),
                        'constraints' =>array(
                            new NotBlank(array('message'=>'Please input required'))
                        )
                    ));

            }
            if($this->inventoryConfig->getIsColor() == 1 ) {

                $builder
                ->add('color', 'entity', array(
                    'required' => true,
                    'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemColor',
                    'empty_value' => '---Choose a color ---',
                    'property' => 'name',
                    'attr' => array('class' => 'span12 select2'),
                    'constraints' => array(
                        new NotBlank(array('message' => 'Please input required'))
                    ),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('v')
                            ->where("v.status = 1");

                    },
                ));

            }
            if($this->inventoryConfig->getIsSize() == 1 ) {

                $builder
                    ->add('size', 'entity', array(
                        'required'    => true,
                        'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemSize',
                        'empty_value' => '---Choose a size/weight ---',
                        'property' => 'name',
                        'attr'=>array('class'=>'span12 select2'),
                        'constraints' =>array(
                            new NotBlank(array('message'=>'Please input required'))
                        ),
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('v')
                                ->where("v.status = 1");
                        },
                    ));

            }

            if($this->inventoryConfig->getIsBrand() == 1 ) {

                $builder
                    ->add('brand', 'text', array(
                        'required'    => true,
                        'mapped'    => false,
                        'attr'=>array('class'=>'span12','list'=>'brands','autocomplete'=>'off'),
                        'constraints' =>array(
                            new NotBlank(array('message'=>'Please input required'))
                        )
                    ));

            }


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

    /**
     * @return mixed
     */
    protected function ItemGroupChoiceList()
    {
        return $itemTypeTree = $this->itemGroup->getFlatInventoryItemGroupTree($this->inventoryConfig);

    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->category->getFlatInventoryCategoryTree($this->inventoryConfig);

    }
}
