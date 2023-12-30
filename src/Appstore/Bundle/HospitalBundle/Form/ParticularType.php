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

class ParticularType extends AbstractType
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
            ->add('service', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a service type---',
                'choices' => array(
                    'Cabin & Word'  => 'Cabin & Word',
                    'Surgery'       => 'Surgery',
                    'Pathology'     => 'Pathology',
                    'Medicine'     => 'Medicine',
                    'Operation'     => 'Operation'
                ),
            ))
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter particular name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('minimumPrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter particular name')))
            ->add('commission','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter particular name')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter particular name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('service', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Service',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('department', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select category---',
                'attr'=>array('class'=>'ExpenseCategory m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Category',
                'property' => 'nestedLabel',
                'choices'=> $this->DepartmentChoiceList()
            ))

            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select category---',
                'attr'=>array('class'=>'ExpenseCategory m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Category',
                'property' => 'nestedLabel',
                'choices'=> $this->PathologyChoiceList()
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
        return 'appstore_bundle_hospitalbundle_particular';
    }

    /**
     * @return mixed
     */
    protected function PathologyChoiceList()
    {
        return $categoryTree = $this->emCategory->getFlatCategoryTree($this->globalOption, $slug = 'pathology');

    }
    /**
     * @return mixed
     */
    protected function DepartmentChoiceList()
    {
        return $categoryTree = $this->emCategory->getFlatCategoryTree($this->globalOption, $slug = 'department');

    }

}
