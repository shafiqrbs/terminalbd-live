<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class StockOutItemType extends AbstractType
{
    /** @var  HospitalConfig */

    public  $config;

    public function __construct(HospitalConfig $config)
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
		        'required'    => false,
		        'property' => 'particularNameCode',
		        'empty_value' => '--- Select medicine/accessories ---',
		        'attr'=>array('class'=>'m-wrap span12 select2 particular'),
		        'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			            ->join("e.service",'s')
				        ->andWhere('s.slug IN (:ids)')
				        ->setParameter('ids', array('medicine','accessories'))
				        ->andWhere("e.hospitalConfig = {$this->config->getId()}")
				        ->orderBy("e.name","ASC");
		        }

	        ))
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsStockOutItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'stockoutitem';
    }
}
