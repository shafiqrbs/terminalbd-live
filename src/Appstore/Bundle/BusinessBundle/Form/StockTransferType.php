<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class StockTransferType extends AbstractType
{

    /** @var  HospitalConfig */
    public  $config;

    public function __construct(BusinessConfig $config)
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
            ->add('businessParticular',
                'text', array('attr'=>array('class'=>'m-wrap span12 select2ParticularName input','placeholder'=>'Enter stock name'),
                'mapped' => false,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('fromWearHouse', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\BusinessBundle\Entity\WearHouse',
                'empty_value' => '---Choose a from wearhouse ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.businessConfig =".$this->config->getId());
                },
            ))
            ->add('toWearHouse', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\BusinessBundle\Entity\WearHouse',
                'empty_value' => '---Choose a to wearhouse ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.businessConfig =".$this->config->getId());
                },
            ))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span12 form-control input-number input','placeholder'=>'Quantity')))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter notes ')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\ProductTransfer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'transfer';
    }
}
