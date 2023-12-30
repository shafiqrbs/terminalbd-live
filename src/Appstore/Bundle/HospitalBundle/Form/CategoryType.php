<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter category name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('parent', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'ServiceCategory m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->CategoryChoiceList()
            ))
            ->add('status')


        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product_bundle_hospitalbundle_category';
    }

    /**
     * @return mixed
     */
    protected function CategoryChoiceList()
    {
        return $this->emCategory->getFlatCategoryTree($this->globalOption);

    }


}
