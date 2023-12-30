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

class DoctorType extends AbstractType
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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter Doctor name'),
                    'constraints' =>array(new NotBlank(array('message'=>'Please enter doctor name'))))
            )
            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter fees')))
            ->add('ipdVisitCharge','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter ipd visit charge')))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter doctor designation')))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter additional phone no')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter valid email')))
            ->add('currentJob','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter current job')))
            ->add('specialist','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Enter specialist')))
            ->add('doctorSignature','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>4,'autocomplete'=>'off','placeholder'=>'Enter Doctor signature'),
                    'constraints' =>array(new NotBlank(array('message'=>'Please enter doctor signature'))))
            )
            ->add('doctorSignatureBangla','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>4,'autocomplete'=>'off','placeholder'=>'Enter Doctor signature')))
            ->add('educationalDegree','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Enter educational degree')))
            ->add('visitTime','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Enter visit time duration')))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'End hour')))
            ->add('weeklyOffDay', 'choice', array(
                'attr'=>array('class'=>'check-list span12'),
                'expanded'      =>true,
                'multiple'      =>true,
                'choices' => array('Sunday' => 'Sunday',  'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'),
            ))
            ->add('room','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter room/cabin name or no')))
            ->add('particularCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter particular code')))
            ->add('file')
            ->add('signatureFile')
            ->add('sendToAccount')
            ->add('assignDoctor', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select doctor from employee list---',
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userDoctor',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join('e.profile','p')
                        ->where("e.enabled = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                        ->andWhere('e.roles LIKE :roles')
                        ->setParameter('roles', '%"ROLE_DOMAIN_HOSPITAL_DOCTOR"%')
                        ->orderBy("p.name","ASC");
                }
            ))
            ->add('assignOperator', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select operator---',
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.enabled = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                        ->andWhere('e.roles LIKE :roles')
                        ->setParameter('roles', '%"ROLE_DOMAIN_OPERATOR"%')
                        ->orderBy("e.id","ASC");
                }
            ))
            /*->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select category---',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->PathologyChoiceList()
            ))*/

            ->add('department', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select department---',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->DepartmentChoiceList()
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
            'cascade_validation' => true,
            'error_bubbling'     => false,
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
