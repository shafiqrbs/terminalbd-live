<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccessoriesStockType extends AbstractType
{


    /** @var  MedicineConfig */

    private $medicineConfig;

    function __construct(MedicineConfig $medicineConfig)
    {
        $this->medicineConfig = $medicineConfig;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 autoComplete2Medicine','placeholder'=>'Enter medicine,herbal,non-medicine,surgery etc'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Purchase price')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'MRP price')))
            ->add('minQuantity','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'Minimum')))
            ->add('maxQuantity','text', array('attr'=>array('class'=>'m-wrap span6','placeholder'=>'Maximum')))
            ->add('rackNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Rack no')))
            ->add('unit', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'property' => 'name',
                'empty_value' => '---Choose a unit ---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('rackNo', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineParticular',
                'empty_value' => '---Choose a rack position ---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","pt")
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig =". $this->medicineConfig->getId())
                        ->andWhere("pt.slug = 'medicine-rack'");
                },
            ))
            ->add('accessoriesBrand', 'entity', array(
                'required'    => false,
                'group_by'  => 'particularType.name',
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineParticular',
                'empty_value' => '---Select herbal/accessories brand ---',
                'property' => 'name',
                'choice_translation_domain' => true,
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","pt")
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig =". $this->medicineConfig->getId())
                        ->andWhere('pt.slug IN (:slugs)')->setParameter('slugs',array('herbal','accessories'));
                },
            ))
            ->add('noDiscount')
            ->add('file')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineStock'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'medicineStock';
    }
}
