<?php

namespace Appstore\Bundle\RestaurantBundle\Form;

use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResturantDamageType extends AbstractType
{

	/** @var  $config RestaurantConfig */

	public  $config;

	public function __construct(RestaurantConfig  $config)
	{
		$this->config = $config;

	}


	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

	        ->add('particular', 'entity', array(
		        'required'    => true,
		        'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Particular',
		        'empty_value' => '---Choose a product ---',
		        'property' => 'name',
		        'attr'=>array('class'=>'span12 m-wrap select2'),
		        'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->where("e.status = 1")
			                  ->andWhere("e.restaurantConfig =".$this->config->getId())
			                  ->andWhere("e.remainingQuantity > 0");
		        },
	        ))
	        ->add('notes','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter notes ')))
	        ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter quantity'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\RestaurantDamage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'damage';
    }


}
