<?php

namespace Setting\Bundle\MediaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PhotoGalleryType extends AbstractType
{

    /** @var GlobalOption  */
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Enter title'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('module', 'entity', array(
                'expanded'      =>true,
                'multiple'      =>true,
                'constraints' => array(
                    new NotBlank(array('message' => 'Please input required'))
                ),
                'class' => 'Setting\Bundle\ToolBundle\Entity\Module',
                'property' => 'name',
                'attr'=>array('class'=>'check-list span6'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
                        ->join('o.siteSettings','m')
                        ->where("o.status = 1")
                        ->andWhere("m.globalOption =".$this->globalOption->getId())
                        ->orderBy('o.name','ASC');
                },
            ))
            ->add('description','textarea', array('attr'=>array('class'=>'span12', 'row' => 8)))
            ->add('file')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\MediaBundle\Entity\PhotoGallery'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_mediabundle_photogallery';
    }
}
