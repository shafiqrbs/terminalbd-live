<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemAttributeType extends AbstractType
{

    /** @var  AssetsCategoryRepository */
    private $em;

    /** @var  GlobalOption */
    private $option;


    function __construct(GlobalOption $option , AssetsCategoryRepository $em)
    {
        $this->em = $em;
        $this->option = $option;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Attribute name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add  attribute name'))
                )))
            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'class' => 'Appstore\Bundle\AssetsBundle\Entity\AssetsCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))
            ->add('placeholder','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Attribute placeholder')))
            ->add('toolTip','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Attribute tooltip')))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AssetsBundle\Entity\ItemAttribute'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'itemAttribute';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->em->getFlatCategoryTree($this->option);

    }
}
