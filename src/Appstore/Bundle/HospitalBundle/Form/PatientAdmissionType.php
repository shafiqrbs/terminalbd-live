<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalAdmissionType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\InvoiceRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PatientAdmissionType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    /** @var  GlobalOption */
    private $globalOption;

    /** @var  InvoiceRepository */
    private $admission;

    /** @var  HmsCategoryRepository */
    private $emCategory;

    function __construct(GlobalOption $globalOption, InvoiceRepository $admission, LocationRepository $location, HmsCategoryRepository $emCategory)
    {
        $this->admission        = $admission;
        $this->globalOption     = $globalOption;
        $this->location         = $location;
        $this->emCategory       = $emCategory;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['data']->getId();
        $builder
            ->add('disease','textarea',
                array('attr'=>array('class'=>'m-wrap span12','required'=> false,'rows' => 4,'placeholder'=>'Add disease')))

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
                        ->orderBy("b.name", "ASC");
                }
            ))
            ->add('diseasesProfile', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup',
                'property' => 'name',
                'empty_value' => '---Select diseases profile---',
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
                'required'    => false,
                'empty_value' => '---Select department---',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory',
                'property' => 'nestedLabel',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select department')),
                ),
                'choices'=> $this->DepartmentChoiceList()
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
                        ->andWhere("b.service = 5")
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
        $builder->add('customer', new CustomerForHospitalAdmissionType( $this->location ));
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
    protected function CabinChoiceList( $id)
    {
        return $this->admission->getExistCabin($this->globalOption->getHospitalConfig()->getId(),$id);

    }

    /**
     * @return mixed
     */
    protected function DepartmentChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 7 /** Department */);

    }


}
