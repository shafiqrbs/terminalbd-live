<?php

namespace Appstore\Bundle\RestaurantBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{

    /** @var  GlobalOption */

    private $globalOption;


    function __construct(GlobalOption $globalOption)
    {

        $this->globalOption         = $globalOption;
        $this->config               = $globalOption->getRestaurantConfig()->getId();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Service',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose product type ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere('e.slug IN (:slugs)')
                        ->setParameter('slugs',array('consuamble','stockable','product'))
                        ->orderBy("e.sorting","ASC");
                }
            ))

            ->add('productGroup', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Particular',
                'property' => 'name',
                'empty_value' => '---Choose product group ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.service",'s')
                        ->where("e.status = 1")
                        ->andWhere('s.slug IN (:slugs)')
                        ->setParameter('slugs',array('product-group'))
                        ->andWhere("e.restaurantConfig = {$this->config}")
                        ->orderBy("e.sorting","ASC");
                }
            ))

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter category name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter category name'))
                ))
            );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'category';
    }


}
