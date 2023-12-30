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

class ItemWebType extends AbstractType
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Add web item name')))
            ->add('content','textarea', array('attr'=>array('class'=>'no-resize span12','rows'=>5)))
            ->add('webPrice','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'web price')))
            ->add('country', 'entity', array(
                'required'      => true,
                'expanded'      =>false,
                'placeholder' => 'Choose made in',
                'class' => 'Setting\Bundle\LocationBundle\Entity\Country',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name','ASC');
                },
            ))
            ->add('brand', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Branding',
                'empty_value' => 'Choose a brand',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->Where("b.status = 1");
                },
            ))
            ->add('ageGroup', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please chose required'))
                ),
                'choices' => array(
                    'Kids' => 'Kids',
                    'Adult' => 'Adult'
                ),
            ))

            ->add('gender', 'choice', array(
                'attr'=>array('class'=>'span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please chose required'))
                ),
                'choices' => array(
                    'Male' => 'Male',
                    'Female' => 'Female'
                ),
            ))
            ->add('material','text', array('attr'=>array('class'=>'m-wrap span12 ')))
            ->add('isWeb')
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
