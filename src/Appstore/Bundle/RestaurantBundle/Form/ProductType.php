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

class ProductType extends AbstractType
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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter product name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter product name'))
                    ))
            )
            ->add('category', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Category',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a category ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                         ->where("e.status = 1")
                         ->andWhere("e.restaurantConfig = {$this->config}")
                        ->orderBy("e.sorting","ASC");
                }
            ))
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>8,'placeholder'=>'Enter product details')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter product price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('productionType', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Production Type---',
                'choices' => array(
                    'pre-production' => 'Pre-production',
                    'post-production' => 'Post-production'
                ),
            ))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter production/purchase price')))
            ->add('file')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\Particular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product';
    }


}
