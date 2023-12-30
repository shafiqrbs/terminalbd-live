<?php

namespace Appstore\Bundle\RestaurantBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Appstore\Bundle\DomainUserBundle\Form\RestaurantCustomerType;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Repository\ParticularRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class RestaurantParticularType extends AbstractType
{

    /** @var  ParticularRepository */
    private $particular;

    /** @var  RestaurantConfig */
    private $restaurantConfig;


    function __construct(RestaurantConfig  $restaurantConfig ,  ParticularRepository $particular)
    {
        $this->particular               = $particular;
        $this->restaurantConfig         = $restaurantConfig->getId();
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
                'empty_value' => '-Choose product name -',
                'attr'=>array('class'=>'span12 m-wrap select2 particular'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.category",'c')
                        ->where("c.status=1")
                        ->andWhere("e.status=1")
                        ->andWhere("e.restaurantConfig ={$this->restaurantConfig}")
                        ->orderBy("c.sorting", "ASC")
                        ->addOrderBy("e.sorting", "ASC");
                }
            ])
            /*  ->add('salesPrice', 'choice', array(
                'label' => 'Choose your car',
                'choices' => $this->ParticularChoiceList(),
            ))*/;

    }



    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restaurant_item';
    }

    protected function ParticularChoiceList()
    {
        return $syndicateTree = $this->particular->getParticularOptionGroup($this->restaurantConfig);

    }





}
