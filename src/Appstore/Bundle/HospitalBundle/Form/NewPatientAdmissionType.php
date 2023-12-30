<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalAdmissionType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalNewAdmissionType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewPatientAdmissionType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    /** @var  HmsCategoryRepository */
    private $emCategory;

    /** @var  GlobalOption */
    private $globalOption;

    /** @var  array */
    private $cabins;


    function __construct(GlobalOption $globalOption , HmsCategoryRepository $emCategory ,  LocationRepository $location, $cabins)
    {
        $this->location         = $location;
        $this->emCategory       = $emCategory;
        $this->globalOption     = $globalOption;
        $this->cabins     = $cabins;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('disease','textarea', array('attr'=>array('class'=>'m-wrap span12','rows' => 6,'placeholder'=>'Disease description'),
            ))
            ->add('cabin', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'name',
                'group_by'  => 'serviceGroup.name',
                'choice_translation_domain' => true,
                'attr'=>array('class'=>'span12 select2 invoiceCabinBooking m-wrap'),
                'empty_value' => '---Select cabin/ward no---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select cabin/ward no'))
                ),
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('b');
                    $qb  ->where("b.status = 1");
                    $qb  ->andWhere("b.service = 2");
                    if($this->cabins){
                        $qb    ->andWhere('b.id NOT IN (:ids)')->setParameter('ids', $this->cabins );
                    }
                    $qb ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId());
                    $qb ->orderBy("b.name", "ASC");
                    return $qb;
                }
            ))
            ->add('diseasesProfile', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup',
                'property' => 'name',
                'empty_value' => '---Choose a diseases profile---',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select diseases profile')),
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.hospitalConfig ='.$this->globalOption->getHospitalConfig()->getId())
                        ->andWhere("e.service = 11")
                        ->andWhere("e.status = 1")
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('department', 'entity', array(
                'required'    => true,
                'empty_value' => '---Choose a department---',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->DepartmentChoiceList()
            ))

            ->add('referredDoctor', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'referredName',
                'attr'=>array('class'=>'span12 select2 m-wrap'),
                'empty_value' => '--- Choose Referred ---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere('b.service IN(:service)') ->setParameter('service',array_values(array(5,6)))
                        ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId())
                        ->orderBy("b.particularCode", "ASC");
                }
            ))

            ->add('assignDoctor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'doctor',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Consultant/Doctor')),
                ),
                'attr'=>array('class'=>'span12 select2 m-wrap'),
                'empty_value' => '--- Choose assign doctor ---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere('b.service IN(:service)') ->setParameter('service',array_values(array(5)))
                        ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))

            ->add('anesthesiaDoctor', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'doctor',
                'attr'=>array('class'=>'span12 select2 m-wrap'),
                'empty_value' => '--- Choose anesthesia doctor ---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.service = 5")
                        ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
            ->add('assistantDoctor', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'doctor',
                'attr'=>array('class'=>'span12 select2 m-wrap'),
                'empty_value' => '--- Choose assistant doctor ---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.service = 5")
                        ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
        ;
        if( $this->globalOption->getHospitalConfig()->isMarketingExecutive() == 1){

            $builder->add('marketingExecutive', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'marketingExecutiveEmployee',
                'attr'=>array('class'=>'span12 select2 m-wrap marketingExecutive'),
                'empty_value' => '--- Choose Marketing Executive ---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere('b.service IN(:service)') ->setParameter('service',array_values(array(14)))
                        ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId())
                        ->orderBy("b.name", "ASC");
                }
            ));
        }
        $builder->add('customer', new CustomerForHospitalNewAdmissionType($this->location ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\Invoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'invoice';
    }

    /**
     * @return mixed
     */
    protected function CabinChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 2 /** Pathology */ );

    }

    /**
     * @return mixed
     */
    protected function DepartmentChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 7 /** Department */);

    }


}
