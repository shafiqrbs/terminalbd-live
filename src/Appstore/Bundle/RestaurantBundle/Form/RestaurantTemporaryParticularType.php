<?php

namespace Appstore\Bundle\RestaurantBundle\Form;


use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RestaurantTemporaryParticularType extends AbstractType
{

    /** @var  RestaurantConfig */
    private $restaurantConfig;


    function __construct(RestaurantConfig  $restaurantConfig)
    {
         $this->restaurantConfig         = $restaurantConfig;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('particular', 'entity', [
                'class'     => 'Appstore\Bundle\RestaurantBundle\Entity\Particular',
                'group_by'  => 'category.name',
                'property'  => 'codeName',
                'empty_value' => '--- Choose Code & Particular ---',
                'attr'=>array('class'=>'span12 m-wrap select2 particular'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.category",'c')
                        ->where("c.status=1")
                        ->andWhere("e.restaurantConfig=".$this->restaurantConfig->getId())
                        ->andWhere("e.status=1")
                        ->orderBy("c.sorting", "ASC")
                        ->addOrderBy("e.sorting", "ASC");
                }
            ]);

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restaurant_particular';
    }

}
