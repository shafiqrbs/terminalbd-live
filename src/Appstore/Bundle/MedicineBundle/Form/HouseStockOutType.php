<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class HouseStockOutType extends AbstractType
{

    /** @var  MedicineConfig  */
    public  $config;

    public function __construct(MedicineConfig $config)
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
            ->add('stockName','text', array('attr'=>array('class'=>'m-wrap span12 select2Stockout','placeholder'=>'Enter stock medicine name')
              ,'constraints' =>array( new NotBlank(array('message'=>'Select stock name')))
            ))
            ->add('wearHouse', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineParticular',
                'empty_value' => '---Stock House ---',
                'property' => 'name',
                'attr'=>array('class'=>' span12 m-wrap'),
                'constraints' =>array( new NotBlank(array('message'=>'Select stock house')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","pt")
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig =".$this->config->getId())
                        ->andWhere("pt.slug = 'wear-house'");
                },
            ))
            ->add('stockOut','number', array('attr'=>array('class'=>'m-wrap span12 form-control','placeholder'=>'quantity')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineStockHouse'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'stockHouse';
    }
}
