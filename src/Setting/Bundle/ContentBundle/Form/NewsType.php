<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsType extends AbstractType
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('authorName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter author name')))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter author designation')))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'ckeditor m-wrap span12','rows'=>8)))
            ->add('moduleCategory', 'entity', array(
                'expanded'      =>true,
                'multiple'      =>true,
                'required'    => false,
                'class' => 'Setting\Bundle\ContentBundle\Entity\ModuleCategory',
                'empty_value' => '---Select category page---',
                'property' => 'name',
                'attr'=>array('class'=>'check-list span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
	                    ->join('o.module', 'module')
	                    ->where("o.status = 1")
	                    ->andWhere("module.slug = :module")
	                    ->setParameter('module', 'news')
                        ->andWhere("o.globalOption =".$this->globalOption->getId())
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
