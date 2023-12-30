<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PageType extends AbstractType
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Enter Name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))

            ->add('menu','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter menu'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('galleryPosition', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'required'    => false,
                'empty_value' => '---Select Gallery Position---',
                'choices' => array('top' => 'Top','bottom' => 'Bottom')
            ))
            ->add('galleryType', 'choice', array(
                'attr'=>array('class'=>'span12'),
                'required'    => false,
                'empty_value' => '---Select Gallery Type---',
                'choices' => array('thumb' => 'Thumb','carousel' => 'Carousel','thumbSlider' => 'Thumb Slider'),
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'span12 ckeditor m-wrap textarea-large','rows' => 30)))
            ->add('shortDescription','textarea', array('attr'=>array('class'=>'span12 m-wrap','rows' => 5)))

            ->add('parent', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ContentBundle\Entity\Page',
                'empty_value' => '---Select parent page---',
                'property' => 'name',
                'attr'=>array('class'=>'select2 span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('o')
                            ->where("o.status = 1")
                            ->andWhere("o.globalOption =".$this->globalOption->getId())
                            ->orderBy('o.name','ASC');
                    },
            ))
            ->add('icon', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Icon',
                'empty_value' => '---Select Icon---',
                'property' => 'name',
                'attr'=>array('class'=>'select2 span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
                        ->where("o.status = 1")
                        ->orderBy('o.name','ASC');
                },
            ))

            ->add('photo_gallery', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\MediaBundle\Entity\PhotoGallery',
                'empty_value' => '---Select Photo Gallery---',
                'property' => 'name',
                'attr'=>array('class'=>'select2 span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('o')
                            ->where("o.status = 1")
                            ->andWhere("o.globalOption =".$this->globalOption->getId())
                            ->orderBy('o.name','ASC');
                    },
            ))
            ->add('status')
            ->add('removeImage')

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_page';
    }
}
