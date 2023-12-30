<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SidebarWidgetPanelType extends AbstractType
{

    private $globalOption;

    public function __construct(GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;
        $this->globalId = $globalOption->getId();
        $this->modules = $globalOption->getSiteSetting()->getModuleIds();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('page', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ContentBundle\Entity\Page',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.module = 1")
                        ->andWhere("e.globalOption = $this->globalId")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('module', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Module',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere('e.id IN(:ids)')
                        ->setParameter('ids',array_values($this->modules))
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('feature', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\Feature',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy('e.name','ASC');
                },
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\SidebarWidgetPanel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_featurewidgetpanel';
    }


}
