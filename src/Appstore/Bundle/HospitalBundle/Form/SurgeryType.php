<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SurgeryType extends AbstractType
{

    /** @var  HospitalConfig */
    private $config;
    function __construct(HospitalConfig $config )
    {
        $this->config     = $config;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter surgery name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter cabin name'))
                    ))
            )
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
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fees'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('surgeryDepartment', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup',
                'property' => 'name',
                'empty_value' => '---Select surgery department---',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select surgery department')),
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.hospitalConfig ='.$this->config->getId())
                        ->andWhere("e.service = 10")
                        ->andWhere("e.status = 1")
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('minimumPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter minimum price')))
            ->add('commission','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter commission')))


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
        return 'appstore_bundle_hospitalbundle_particular';
    }



}
