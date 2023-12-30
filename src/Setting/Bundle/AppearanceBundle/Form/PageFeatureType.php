<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PageFeatureType extends AbstractType
{

    private $globalOption;

    public function __construct(GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'span12 m-wrap','placeholder'=>'Enter full name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'span12 m-wrap')))
            ->add('menu', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\Menu',
                'empty_value' => '---Select page---',
                'property' => 'menu',
                'attr'=>array('class'=>'m-wrap span12 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->globalOption->getId())
                        ->orderBy('e.menu','ASC');
                },
            ))
            ->add('customUrl','text', array('attr'=>array('class'=>'span12 m-wrap','placeholder'=>'Custom URL path')))
            ->add('buttonName','text', array('attr'=>array('class'=>'span12 m-wrap','placeholder'=>'Button Text')))
            ->add('buttonBg','text', array('attr'=>array('class'=>'span10 m-wrap colorpicker-default','placeholder'=>'Button Bg')))
            ->add('captionBgColor','text', array('attr'=>array('class'=>'span11 m-wrap colorpicker-default','placeholder'=>'Caption Background Color')))
            ->add('captionFontColor','text', array('attr'=>array('class'=>'span11 m-wrap colorpicker-default','placeholder'=>'Caption Font Color')))

            ->add('captionPosition', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap'),
                'empty_value' => '---Caption Position ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
	                'top-left'               => 'Top Left',
	                'top-center'             => 'Top Center',
	                'top-right'              => 'Top Right',
	                'center-left'            => 'Center Left',
	                'center-center'          => 'Center Center',
	                'center-right'           => 'Center Right',
	                'bottom-left'            => 'Bottom Left',
	                'bottom-center'          => 'Bottom Center',
	                'bottom-right'           => 'Bottom Right',

                ),
            ))
            ->add('targetTo', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap targetTo'),
                'empty_value' => '---Select Target URL ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'Page'              => 'Page',
                ),
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\Feature'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_feature';
    }


}
