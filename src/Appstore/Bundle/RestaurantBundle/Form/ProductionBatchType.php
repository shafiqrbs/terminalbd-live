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

class ProductionBatchType extends AbstractType
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

            ->add('productionItem', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Particular',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a production item ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join('e.service','s')
                         ->where("e.status = 1")
                         ->andWhere("e.restaurantConfig = {$this->config}")
                         ->andWhere("e.productionType = 'pre-production'")
                         ->orderBy("e.sorting","ASC");
                }
            ))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter initiate quantity','constraints' =>array(
        new NotBlank(array('message'=>'Please select required'))
    ))))
            ->add('issueQuantity','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter issue quantity','constraints' =>array(
                new NotBlank(array('message'=>'Please select required'))
            ))))
            ->add('damageQuantity','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter damage quantity')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\ProductionBatch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'productionBatch';
    }


}
