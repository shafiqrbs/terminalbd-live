<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PathologyType extends AbstractType
{


    /** @var  HmsCategoryRepository */
    private $emCategory;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(HmsCategoryRepository $emCategory , GlobalOption $globalOption)
    {
        $this->emCategory = $emCategory;
        $this->globalOption = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter test name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
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
            ->add('sepcimen','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter sample collection')))
            ->add('testDuration')
            ->add('reportFormat')
            ->add('reportUnitHide')
            ->add('discountValid')
            ->add('isAttachment')
            ->add('additionalField')
            ->add('isReportContent')
            ->add('isMachineFormat')
            ->add('reportMachineName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter report machine name')))
            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('instruction','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>'3','placeholder'=>'Enter test related any instruction for patient')))
            ->add('reportContent','textarea', array('attr'=>array('class'=>'big-editor ','rows'=>15,'placeholder'=>'Enter test related any instruction for patient')))
            ->add('minimumPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter minimum price')))
            ->add('reportHeight','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter report height')))
            ->add('commission','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter commission')))
            ->add('overHeadCost','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter over head cost')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('assignOperator', 'entity', array(
                'required'    => false,
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.enabled = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                 /*       ->join('e.profile','p')*/
                 /*       ->join('p.designation','designation')*/
                 /*       ->where("e.enabled = 1")*/
                 /*       ->andWhere("designation.slug = 'operator'")*/
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('department', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select department---',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->DepartmentChoiceList()
            ))

            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select pathology---',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->PathologyChoiceList()
            ));
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

    /**
     * @return mixed
     */
    protected function PathologyChoiceList()
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
