<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CabinType extends AbstractType
{


    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption)
    {
        $this->globalOption     = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('serviceGroup', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup',
                'property' => 'name',
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Select cabin/ward ---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.service = 2")
                        ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
            ->add('accountHead', 'entity', array(
                'class'     => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'group_by'  => 'parent.name',
                'property'  => 'name',
                'empty_value' => '---Choose a account---',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.parent",'c')
                        ->where("e.status = 1")
                        ->andWhere("c.isParent =1")
                        ->orderBy("e.name", "ASC");
                }
            ))
            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter content')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'particular';
    }


}
